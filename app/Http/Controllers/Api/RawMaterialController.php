<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'List all raw materials',
            'data' => $materials
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'unit_measurement' => 'required|in:gram,ml,pcs',
        ]);

        $rawMaterial = RawMaterial::create([
            'name' => $request->name,
            'unit_measurement' => $request->unit_measurement,
            'current_stock' => 0
    ]);

        return response()->json([
            'success' => true,
            'message' => 'Raw Material Added Successfully',
            'data' => $rawMaterial
        ], 201);
    }
}
