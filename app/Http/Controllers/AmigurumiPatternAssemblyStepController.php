<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AmigurumiPattern;
use App\Models\AmigurumiPatternAssemblyStep;

class AmigurumiPatternAssemblyStepController extends Controller
{
    public function index(AmigurumiPattern $pattern)
    {
        return $pattern->assemblySteps()->with('images')->get();
    }

    public function store(Request $request, AmigurumiPattern $pattern)
    {
        $step = $pattern->assemblySteps()->create([
            'text' => $request->input('text'),
            'order' => $request->input('order', 0),
        ]);

        return response()->json($step->load('images'));
    }

    public function update(Request $request, AmigurumiPatternAssemblyStep $step)
    {
        $step->update($request->only(['text', 'order']));
        return response()->json($step->load('images'));
    }

    public function destroy(AmigurumiPatternAssemblyStep $step)
    {
        $step->images->each->delete();
        $step->delete();

        return response()->noContent();
    }
}
