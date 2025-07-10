<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiPattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmigurumiPatternController extends Controller
{
    public function index()
    {
        $patterns = AmigurumiPattern::with('sections.rows')->orderByDesc('created_at')->get();
        return view('amigurumi.patterns.index', compact('patterns'));
    }


    public function show($id)
    {
        $pattern = AmigurumiPattern::with('sections.rows')->find($id);
        if (!$pattern) {
            return response()->json(['message' => 'Pattern not found'], 404);
        }
        return response()->json($pattern);
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

    public function update(Request $request, $id)
    {
        $pattern = AmigurumiPattern::find($id);
        if (!$pattern) {
            return response()->json(['message' => 'Pattern not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'image_path' => 'nullable|string|max:255',
            'yarn_description' => 'sometimes|required|string',
            'tools_description' => 'sometimes|required|string',
        ]);

        $pattern->update($validated);
        return response()->json($pattern);
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
    
}
