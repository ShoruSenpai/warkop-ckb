<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Models\Category;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'base_price' => 'required|numeric',
            'stock_type' => 'required|in:static,recipe,untracked'
        ]);

        $category = Category::FirstOrCreate(['name' => $request->category_name]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => $request->name,
            'base_price' => $request->base_price,
            'stock_type' => $request->stock_type,
            'stock' => 0,
            'status' => 'available',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }
}
