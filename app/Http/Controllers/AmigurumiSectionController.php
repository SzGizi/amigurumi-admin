<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiSection;
use App\Models\AmigurumiPattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAmigurumiSectionRequest;
use App\Http\Requests\UpdateAmigurumiSectionRequest;
use App\Http\Resources\AmigurumiSectionResource;

class AmigurumiSectionController extends Controller
{
    public function store(StoreAmigurumiSectionRequest  $request, AmigurumiPattern $pattern)
    {
      
        $data = $request->validated();

        $pattern->amigurumiSections()->create($data);

        return response()->json(['message' => 'Section created successfully'], 201);
    }

    public function update(UpdateAmigurumiSectionRequest $request, $id)
    {
        
        $section = AmigurumiSection::find($id);
        
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $data = $request->validated();
    
        $section->update($data);
        return response()->json(['message' => 'Section updated successfully'], 201);

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
