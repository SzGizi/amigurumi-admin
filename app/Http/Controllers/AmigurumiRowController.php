<?php

namespace App\Http\Controllers;

use App\Models\AmigurumiRow;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmigurumiRowController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amigurumi_section_id' => 'required|exists:amigurumi_sections,id',
            'row_number' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $row = AmigurumiRow::create($validated);
        return response()->json($row, 201);
    }

    public function update(Request $request, $id)
    {
        $row = AmigurumiRow::find($id);
        if (!$row) {
            return response()->json(['message' => 'Row not found'], 404);
        }

        $validated = $request->validate([
            'row_number' => 'sometimes|required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $row->update($validated);
        return response()->json($row);
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
