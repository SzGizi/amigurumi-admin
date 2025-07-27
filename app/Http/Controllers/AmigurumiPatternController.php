<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiPattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AmigurumiPatternResource;
use App\Http\Resources\AmigurumiSectionResource;
use App\Http\Requests\UpdateAmigurumiPatternRequest;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ImageService;
use App\Models\Image;


class AmigurumiPatternController extends Controller
{

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $patterns = AmigurumiPattern::with('amigurumiSections.amigurumiRows')->orderByDesc('created_at')->get();
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

        $pattern = AmigurumiPattern::create($validated);
        // Visszairányítás a minták listájára flash üzenettel
        return redirect()->route('amigurumi-patterns.index')
                     ->with('success', __('Pattern created successfully.'));
    
    }

    public function update(UpdateAmigurumiPatternRequest $request, AmigurumiPattern $amigurumiPattern)
    {
    
        Log::info('Beérkezett PUT kérés', $request->all());
        //Log::info('Sections:', $request->input('sections'));
    
        

        $amigurumiPattern->update($request->only([
            'title',
            'yarn_description',
            'tools_description',
        ]));


        // Régiek törlése
        foreach ($amigurumiPattern->amigurumiSections as $section) {
            $section->amigurumiRows()->delete();
            $section->delete();
        }

        // Újak mentése
        $sections = $request->input('sections', []);

        foreach ($sections as $sectionData) {
            $section = $amigurumiPattern->amigurumiSections()->create([
                'title' => $sectionData['title'] ?? '',
                'order' => $sectionData['order'] ?? 0,
                'amigurumi_pattern_id' => $amigurumiPattern->id
            ]);

            foreach ($sectionData['rows'] ?? [] as $rowData) {
                $hasComment = !empty($rowData['showComment']) && $rowData['showComment'];
                $section->amigurumiRows()->create([
                    'row_number' => $rowData['row_number'] ?? '',
                    'instructions' => $rowData['instructions'] ?? '',
                    'stitch_number' => $rowData['stitch_number'] ?? null,
                    'comment' => $hasComment ? ($rowData['comment'] ?? '') : '',
                    'order' => $rowData['order'] ?? null,
                    'amigurumi_section_id' => $section->id
                ]);
            }
          
        }

        $deletedImageIds = explode(',', $request->input('deleted_image_ids', ''));

        foreach ($deletedImageIds as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                $this->imageService->deleteImage($image);
            }
        }
       
        // Új képek feltöltése
        if ($request->main_image_id) {
            $image = Image::find($request->main_image_id);
            if ($image && $image->imageable_id == $amigurumiPattern->id) {
                $this->imageService->setMainImage($image);
            }
        }
        
       return response()->json([
            'message' => __('Pattern updated successfully.'),
            'pattern' => $amigurumiPattern->load('amigurumiSections.amigurumiRows')
        ]);
    }

    public function destroy($id)
    {
        $pattern = AmigurumiPattern::find($id);
        if (!$pattern) {
            return response()->json(['message' => 'Pattern not found'], 404);
        }

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
        $amigurumiPattern->load('amigurumiSections.amigurumiRows');

        return view('amigurumi.patterns.show', compact('amigurumiPattern'));
    }
   
    public function edit(AmigurumiPattern $amigurumiPattern)
    {
        $amigurumiPattern->load('amigurumiSections.amigurumiRows');

        return view('amigurumi.patterns.edit', [
            'amigurumiPattern' => $amigurumiPattern,
            'sectionsJson' => AmigurumiSectionResource::collection($amigurumiPattern->amigurumiSections)->toJson(),
        ]);
        //return view('amigurumi.patterns.edit', compact('amigurumiPattern'));
    }
    public function generatePdf(Request $request)
    {
        $data = $request->all();

        // opcionális validáció itt
        // Validator::make($data, [...])->validate();

        $pdf = Pdf::loadView('pdf.pattern', ['pattern' => $data]);

        // PDF fájl letöltésre
        return $pdf->download('pattern.pdf');

        // Ha base64-ben szeretnéd visszaküldeni:
        // return response()->json(['pdf' => base64_encode($pdf->output())]);
    }
}
