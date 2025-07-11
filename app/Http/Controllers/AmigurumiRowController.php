<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiSection;
use App\Models\AmigurumiRow;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAmigurumiRowRequest;
use App\Http\Requests\UpdateAmigurumiRowRequest;
use App\Http\Resources\AmigurumiRowResource;

class AmigurumiRowController extends Controller
{
    public function store(StoreAmigurumiRowRequest $request,AmigurumiSection $amigurumi_section)
    {
        
        $data = $request->validated();

        $amigurumi_section->amigurumiRows()->create($data);

        return response()->json(['message' => 'Section created successfully'], 201);
    }

    public function update(UpdateAmigurumiRowRequest $request, $id)
    {

        $row = AmigurumiRow::find($id);
        
        if (!$row) {
            return response()->json(['message' => 'Row not found'], 404);
        }

        $data = $request->validated();
    
        $row->update($data);
        return response()->json(['message' => 'Row updated successfully'], 201);
    }

    public function destroy($id)
    {
        $row = AmigurumiRow::find($id);
        if (!$row) {
            return response()->json(['message' => 'Row not found'], 404);
        }

        $row->delete();
        return response()->json(['message' => 'Row deleted successfully']);
    }
}
