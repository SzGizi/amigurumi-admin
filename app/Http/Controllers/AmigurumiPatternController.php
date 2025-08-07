<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiPattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AmigurumiPatternResource;
use App\Http\Resources\AmigurumiSectionResource;
use App\Http\Requests\UpdateAmigurumiPatternRequest;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPdf;




class AmigurumiPatternController extends Controller
{

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        
       $patterns = AmigurumiPattern::where('user_id', Auth::id())
            ->with('amigurumiSections.amigurumiRows')
            ->latest()
            ->get();
        return view('amigurumi.patterns.index', compact('patterns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'nullable|string|max:255',
            'yarn_description' => 'required|string',
            'tools_description' => 'required|string',
        ]); 

        //$pattern = AmigurumiPattern::create($validated);
        $pattern = new AmigurumiPattern($validated);
   
       
      
        $pattern->user_id = Auth::id();

        $pattern->save();
        // Visszairányítás a minták listájára flash üzenettel
        return redirect()->route('amigurumi-patterns.index')
                     ->with('success', __('Pattern created successfully.'));
                   
    
    }

    public function update(UpdateAmigurumiPatternRequest $request, AmigurumiPattern $amigurumiPattern)
    {
          Log::info('Beérkezett PUT kérés: ' . var_export($request->all(), true));
        //Log::info('Sections:', $request->input('sections'));
        if ($amigurumiPattern->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $amigurumiPattern->update($request->only([
            'title',
            'yarn_description',
            'tools_description',
            'final_size',
            'difficulty',
            'introduction',
            'abbreviations',
        ]));

        $sections = $request->input('sections', []);
        $assemblySteps = $request->input('assemblySteps', []);

        $existingSectionIds = [];
        $existingRowIds = [];
        $existingAssemblyStepIds = [];

        $createdSectionMap = []; // uid => new_id
        $createdRowMap = [];     // uid => new_id
        $createdAssemblyStepMap = [];     // uid => new_id

        foreach ($sections as $sectionData) {
            // Ha van section id → frissítünk, egyébként létrehozunk
            if (!empty($sectionData['id'])) {
                $section = $amigurumiPattern->amigurumiSections()->find($sectionData['id']);

                if ($section) {
                    $section->update([
                        'title' => $sectionData['title'] ?? '',
                        'order' => $sectionData['order'] ?? 0,
                    ]);
                    $existingSectionIds[] = $section->id;
                } else {
                    continue; // ha nem található a section ID, nem csinálunk semmit
                }
            } else {
                $section = $amigurumiPattern->amigurumiSections()->create([
                    'title' => $sectionData['title'] ?? '',
                    'order' => $sectionData['order'] ?? 0,
                ]);
                $existingSectionIds[] = $section->id;

                if (!empty($sectionData['uid'])) {
                    $createdSectionMap[$sectionData['uid']] = $section->id;
                }
            }

            // Rows kezelése
            $rows = $sectionData['rows'] ?? [];
            foreach ($rows as $rowData) {
                $hasComment = !empty($rowData['showComment']) && $rowData['showComment'];
                $hasColorChange = !empty($rowData['showColorChange']) && $rowData['showColorChange'];

                if (!empty($rowData['id'])) {
                    $row = $section->amigurumiRows()->find($rowData['id']);
                    if ($row) {
                        $row->update([
                            'row_number' => $rowData['row_number'] ?? '',
                            'instructions' => $rowData['instructions'] ?? '',
                            'stitch_number' => $rowData['stitch_number'] ?? null,
                            'comment' => $hasComment ? ($rowData['comment'] ?? '') : '',
                            'color_change' => $hasColorChange ? ($rowData['color_change'] ?? '') : '',
                            'order' => $rowData['order'] ?? null,
                        ]);
                        $existingRowIds[] = $row->id;
                    }
                } else {
                    $newRow = $section->amigurumiRows()->create([
                        'row_number' => $rowData['row_number'] ?? '',
                        'instructions' => $rowData['instructions'] ?? '',
                        'stitch_number' => $rowData['stitch_number'] ?? null,
                        'comment' => $hasComment ? ($rowData['comment'] ?? '') : '',
                        'color_change' => $hasColorChange ? ($rowData['color_change'] ?? '') : '',
                        'order' => $rowData['order'] ?? null,
                    ]);
                    $existingRowIds[] = $newRow->id;
                       if (!empty($rowData['uid'])) {
                            $createdRowMap[$rowData['uid']] = $newRow->id;
                        }
                }
            }
        }

        foreach ($assemblySteps as $assemblyStepData) {
            // Ha van section id → frissítünk, egyébként létrehozunk
            if (!empty($assemblyStepData['id'])) {
                $assemblyStep = $amigurumiPattern->assemblySteps()->find($assemblyStepData['id']);

                if ($assemblyStep) {
                    $assemblyStep->update([
                        'text' => $assemblyStepData['text'] ?? '',
                        'order' => $assemblyStepData['order'] ?? 0,
                    ]);
                    $existingAssemblyStepIds[] = $assemblyStep->id;
                } else {
                    continue; // ha nem található a section ID, nem csinálunk semmit
                }
            } else {
                $assemblyStep = $amigurumiPattern->assemblySteps()->create([
                    'text' => $assemblyStepData['text'] ?? '',
                    'order' => $assemblyStepData['order'] ?? 0,
                ]);
                $existingAssemblyStepIds[] = $assemblyStep->id;

                if (!empty($assemblyStepData['uid'])) {
                    $createdAssemblyStepMap[$assemblyStepData['uid']] = $assemblyStep->id;
                }
            }
        }

        // ✳️ Opcionális: nem szereplő section-ök és row-k törlése (ha kell)
        $amigurumiPattern->amigurumiSections()
            ->whereNotIn('id', $existingSectionIds)
            ->each(function ($section) {
                $section->amigurumiRows()->delete();
                $section->delete();
            });

        foreach ($amigurumiPattern->amigurumiSections as $section) {
            $section->amigurumiRows()
                ->whereNotIn('id', $existingRowIds)
                ->delete();
        }

        $amigurumiPattern->assemblySteps()
            ->whereNotIn('id', $existingAssemblyStepIds)
            ->each(function ($assemblyStep) {
                $assemblyStep->delete();
            });

        // Képek törlése
        $deletedImageIds = explode(',', $request->input('deleted_image_ids', ''));

        foreach ($deletedImageIds as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                $this->imageService->deleteImage($image);
            }
        }

        // Main image beállítása
        if ($request->main_image_id) {
            $image = Image::find($request->main_image_id);
            if ($image && $image->imageable_id == $amigurumiPattern->id) {
                $this->imageService->setMainImage($image);
            }
        }

        return response()->json([
            'message' => __('Pattern updated successfully.'),
            'pattern' => $amigurumiPattern->load('amigurumiSections.amigurumiRows'),
            'created_section_ids' => $createdSectionMap,
            'created_row_ids' => $createdRowMap,
            'created_assembly_step_ids' => $createdAssemblyStepMap,
        ]);
    }


    public function destroy($id)
    {
        $pattern = AmigurumiPattern::find($id);
        
        if (!$pattern) {
            return response()->json(['message' => 'Pattern not found'], 404);
        }
        if ($pattern->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $pattern->amigurumiSections()
            ->get() 
            ->each(function ($section) {
                $section->amigurumiRows()->delete();
                $section->delete(); 
            });

        $pattern->delete();
        return redirect()->route('amigurumi-patterns.index')
                     ->with('success', __('Pattern deleted successfully.'));

    }
    public function create()
    {
        return view('amigurumi.patterns.create');
    }
    public function show(AmigurumiPattern $amigurumiPattern)
    {
        if ($amigurumiPattern->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $amigurumiPattern->load('amigurumiSections.amigurumiRows');

        return view('amigurumi.patterns.show', compact('amigurumiPattern'));
    }
   
    public function edit(AmigurumiPattern $amigurumiPattern)
    {
        if ($amigurumiPattern->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $amigurumiPattern->load('amigurumiSections.amigurumiRows');

        return view('amigurumi.patterns.edit', [
            'amigurumiPattern' => $amigurumiPattern,
            'sectionsJson' => AmigurumiSectionResource::collection($amigurumiPattern->amigurumiSections)->toJson(),
        ]);

        
    }
    
    

    public function generatePdf(Request $request)
    {
        
        $data = $request->all();

        
        // Feltételezem, hogy $data['id'] a pattern azonosítója
        $pattern =  $pattern = AmigurumiPattern::find($data['id']);

        if ($pattern) {
            $data['user'] = [
                'creator_name' => $pattern->user->creator_name,   
                'logo' => $pattern->user->logo,                    
            ];
        } else {
            $data['user'] = [
                'creator_name' => 'Unknown',
                'logo' => null,
            ];
        }

        // HTML renderelés a Blade sablonból
        $html = view('pdf.pattern', ['pattern' => $data])->render();

     

         $pdf = SnappyPdf::loadHTML($html) ->setOptions([ 
                'encoding' => 'utf-8',
                'page-size' => 'A4',
                // 🆕 PDF margók teljes kikapcsolása
                'margin-top' => '20mm',
                'margin-bottom' => '20mm',
                'margin-left' => '15mm',
                'margin-right' => '15mm',

                // 🆕 Háttér engedélyezése (CSS-ből is)
                'background' => true,

                'no-outline' => true,
                'footer-left' => '© ' . date('Y') .' - '. $data['title'],
                'footer-center' => '[page]',
                'footer-right' => 'Generated by Amigurumi Admin',
                'footer-spacing' => 5,

                'footer-font-size' => 7,
                'enable-local-file-access' => true,
                'enable-internal-links' => true,
                'no-outline' => true,
                'no-stop-slow-scripts' => true,
                
            ]);

        return $pdf->download('pattern.pdf');
    }

    



}
