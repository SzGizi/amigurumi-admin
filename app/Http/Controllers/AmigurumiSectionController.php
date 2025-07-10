<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiSection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmigurumiSectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amigurumi_pattern_id' => 'required|exists:amigurumi_patterns,id',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $section = AmigurumiSection::create($validated);
        return response()->json($section, 201);
    }

    public function update(Request $request, $id)
    {
        $section = AmigurumiSection::find($id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $section->update($validated);
        return response()->json($section);
    }

    public function destroy($id)
    {
        $section = AmigurumiSection::find($id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $section->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }
}
