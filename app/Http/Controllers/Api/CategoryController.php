<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Get Categories',
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        // Only owner can create a category.
        if ($request->user()->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create a category.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name',
            ],
        ]);

        $category = Category::create([
            'name' => trim($validated['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }
}
