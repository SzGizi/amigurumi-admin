<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiPattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AmigurumiPatternResource;
use App\Http\Requests\UpdateAmigurumiPatternRequest;

class AmigurumiPatternController extends Controller
{
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
      // dd($request->all());
        // Frissítés
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
            ]);

            foreach ($sectionData['rows'] ?? [] as $rowData) {
                $section->amigurumiRows()->create([
                    'row_number' => $rowData['row_number'] ?? 0,
                    'instructions' => $rowData['instructions'] ?? '',
                    'stitch_number' => $rowData['stitch_number'] ?? null,
                    'comment' => $rowData['comment'] ?? ''
                ]);
            }
          
        }

        // Csak ez maradjon itt!
        return redirect()
            ->route('amigurumi-patterns.edit', $amigurumiPattern)
            ->with('success', __('Pattern updated successfully.'));
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
        return view('amigurumi.patterns.edit', compact('amigurumiPattern'));
    }
}
