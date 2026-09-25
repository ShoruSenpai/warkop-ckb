<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPackaging;
use App\Models\ProductRecipe;
use App\Models\SupplierPurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::with([
            'category:id,name',
            'recipes.rawMaterial:id,name,unit_measurement,current_stock',
            'packagings:id,product_id,purchase_unit,conversion_factor,is_active',
        ])
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return $this->formatProduct($product);
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Get Products',
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',

            'name' => 'required|string|max:100',

            'description' => 'nullable|string',

            'base_price' => 'required|numeric|min:0',

            'stock_type' => 'required|in:static,recipe,untracked',

            'image_url' => 'nullable|string|max:500',

            'is_recommended' => 'nullable|boolean',

            'status' => 'nullable|in:available,sold_out,disabled',

            /*
             * Recipe wajib diisi kalau stock_type = recipe.
             */
            'recipes' => 'required_if:stock_type,recipe|array|min:1',

            'recipes.*.raw_material_id' =>
                'required|integer|exists:raw_materials,id|distinct',

            'recipes.*.amount_needed' =>
                'required|numeric|min:0.01',
        ]);

        try {
            $responseData = DB::transaction(function () use ($validated) {

                $product = Product::create([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'base_price' => $validated['base_price'],

                    /*
                     * Untuk:
                     * static   -> stok aktual disimpan di sini
                     * recipe   -> akan dihitung dinamis
                     * untracked -> tidak dipakai
                     */
                    'stock' => 0,

                    'stock_type' => $validated['stock_type'],

                    'image_url' => $validated['image_url'] ?? null,

                    'is_recommended' =>
                        $validated['is_recommended'] ?? false,

                    'status' =>
                        $validated['status'] ?? 'available',
                ]);

                /*
                 * Simpan recipe jika jenis produk = recipe.
                 */
                if (
                    $validated['stock_type'] === 'recipe'
                    && !empty($validated['recipes'])
                ) {
                    foreach ($validated['recipes'] as $recipe) {

                        ProductRecipe::create([
                            'product_id' => $product->id,
                            'raw_material_id' =>
                                $recipe['raw_material_id'],
                            'amount_needed' =>
                                $recipe['amount_needed'],
                        ]);
                    }
                }

                /*
                 * Load ulang semua relasi sebelum transaction selesai.
                 */
                $product->load([
                    'category:id,name',
                    'recipes.rawMaterial:id,name,unit_measurement,current_stock',
                    'packagings:id,product_id,purchase_unit,conversion_factor,is_active',
                ]);

                /*
                 * Format response sebelum commit.
                 * Kalau casting / perhitungan error, transaction rollback.
                 */
                return $this->formatProduct($product);
            });

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $responseData,
            ], 201);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Gagal membuat produk. Silakan periksa data yang dimasukkan.',
            ], 500);
        }
    }

    public function show(Product $product)
    {
        $product->load([
            'category:id,name',
            'recipes.rawMaterial:id,name,unit_measurement,current_stock',
            'packagings:id,product_id,purchase_unit,conversion_factor,is_active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Get Product',
            'data' => $this->formatProduct($product),
        ]);
    }

    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',

            'name' => 'required|string|max:100',

            'description' => 'nullable|string',

            'base_price' => 'required|numeric|min:0',

            'stock_type' => 'required|in:static,recipe,untracked',

            'image_url' => 'nullable|string|max:500',

            'is_recommended' => 'nullable|boolean',

            'status' => 'nullable|in:available,sold_out,disabled',

            'recipes' => 'required_if:stock_type,recipe|array|min:1',

            'recipes.*.raw_material_id' =>
                'required|integer|exists:raw_materials,id|distinct',

            'recipes.*.amount_needed' =>
                'required|numeric|min:0.01',
        ]);

        try {
            $responseData = DB::transaction(function () use (
                $validated,
                $product
            ) {

                $product->update([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'base_price' => $validated['base_price'],
                    'stock_type' => $validated['stock_type'],
                    'image_url' => $validated['image_url'] ?? null,

                    'is_recommended' =>
                        $validated['is_recommended'] ?? false,

                    'status' =>
                        $validated['status'] ?? $product->status,
                ]);

                /*
                 * Recipe selalu disinkronkan dengan isi form.
                 *
                 * Kalau bukan recipe, semua recipe lama dihapus.
                 */
                $product->recipes()->delete();

                if (
                    $validated['stock_type'] === 'recipe'
                    && !empty($validated['recipes'])
                ) {
                    foreach ($validated['recipes'] as $recipe) {

                        ProductRecipe::create([
                            'product_id' => $product->id,
                            'raw_material_id' =>
                                $recipe['raw_material_id'],
                            'amount_needed' =>
                                $recipe['amount_needed'],
                        ]);
                    }
                }

                $product->load([
                    'category:id,name',
                    'recipes.rawMaterial:id,name,unit_measurement,current_stock',
                    'packagings:id,product_id,purchase_unit,conversion_factor,is_active',
                ]);

                return $this->formatProduct($product);
            });

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $responseData,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Gagal memperbarui produk. Silakan periksa data yang dimasukkan.',
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        /*
         * Jangan hapus product yang sudah menjadi bagian
         * dari history transaksi / master lain.
         */

        $hasOrderHistory = DB::table('order_items')
            ->where('product_id', $product->id)
            ->exists();

        $hasPurchaseHistory = DB::table('supplier_purchase_items')
            ->where('product_id', $product->id)
            ->exists();

        $hasRecipes = DB::table('product_recipes')
            ->where('product_id', $product->id)
            ->exists();

        $hasPackagings = DB::table('product_packagings')
            ->where('product_id', $product->id)
            ->exists();

        $hasOptions = DB::table('product_option_groups')
            ->where('product_id', $product->id)
            ->exists();

        $hasDiscounts = DB::table('product_discounts')
            ->where('product_id', $product->id)
            ->exists();

        if (
            $hasOrderHistory ||
            $hasPurchaseHistory ||
            $hasRecipes ||
            $hasPackagings ||
            $hasOptions ||
            $hasDiscounts
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Produk tidak dapat dihapus karena sudah digunakan dalam data lain atau memiliki riwayat transaksi.',
            ], 409);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT PACKAGING
    |--------------------------------------------------------------------------
    */

    public function packagingIndex(Product $product)
    {
        $packagings = $product->packagings()
            ->orderBy('purchase_unit')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Get Product Packagings',
            'data' => $packagings,
        ]);
    }

    public function packagingStore(
        Request $request,
        Product $product
    ) {
        /*
         * Packaging pembelian hanya berlaku untuk
         * produk dengan stock_type = static.
         */
        if ($product->stock_type !== 'static') {
            return response()->json([
                'success' => false,
                'message' =>
                    'Packaging pembelian hanya dapat digunakan untuk produk static.',
            ], 422);
        }

        $validated = $request->validate([
            'purchase_unit' =>
                'required|string|max:50',

            'conversion_factor' =>
                'required|numeric|min:0.001',

            'is_active' =>
                'nullable|boolean',
        ]);

        $exists = $product->packagings()
            ->where('purchase_unit', $validated['purchase_unit'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Satuan pembelian tersebut sudah terdaftar untuk produk ini.',
            ], 422);
        }

        $packaging = $product->packagings()->create([
            'purchase_unit' => $validated['purchase_unit'],
            'conversion_factor' =>
                $validated['conversion_factor'],
            'is_active' =>
                $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product packaging created successfully.',
            'data' => $packaging,
        ], 201);
    }

    public function packagingUpdate(
        Request $request,
        Product $product,
        ProductPackaging $packaging
    ) {
        /*
         * Pastikan packaging memang milik product tersebut.
         */
        if ($packaging->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Packaging tidak ditemukan untuk produk ini.',
            ], 404);
        }

        $validated = $request->validate([
            'purchase_unit' =>
                'required|string|max:50',

            'conversion_factor' =>
                'required|numeric|min:0.001',

            'is_active' =>
                'nullable|boolean',
        ]);

        $exists = $product->packagings()
            ->where('purchase_unit', $validated['purchase_unit'])
            ->where('id', '!=', $packaging->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Satuan pembelian tersebut sudah terdaftar untuk produk ini.',
            ], 422);
        }

        $packaging->update([
            'purchase_unit' =>
                $validated['purchase_unit'],

            'conversion_factor' =>
                $validated['conversion_factor'],

            'is_active' =>
                $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product packaging updated successfully.',
            'data' => $packaging->fresh(),
        ]);
    }

    public function packagingDestroy(
        Product $product,
        ProductPackaging $packaging
    ) {
        if ($packaging->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Packaging tidak ditemukan untuk produk ini.',
            ], 404);
        }

        /*
         * Jangan hapus packaging yang sudah dipakai
         * dalam transaksi pembelian.
         */
        $used = SupplierPurchaseItem::where(
            'product_packaging_id',
            $packaging->id
        )->exists();

        if ($used) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Packaging tidak dapat dihapus karena sudah digunakan dalam riwayat pembelian.',
            ], 409);
        }

        $packaging->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product packaging deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,

            'category_id' => $product->category_id,

            'category' => $product->category
                ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ]
                : null,

            'name' => $product->name,

            'description' => $product->description,

            'base_price' => $product->base_price,

            /*
             * Ini stok yang akan dipakai frontend.
             *
             * static    -> products.stock
             * recipe    -> hasil hitung bahan baku
             * untracked -> null
             */
            'stock' => $this->calculateStock($product),

            'stock_type' => $product->stock_type,

            'image_url' => $product->image_url,

            'is_recommended' => $product->is_recommended,

            'status' => $product->status,

            'recipes' => $product->recipes
                ->map(function ($recipe) {
                    return [
                        'id' => $recipe->id,
                        'raw_material_id' =>
                            $recipe->raw_material_id,
                        'raw_material' => $recipe->rawMaterial
                            ? [
                                'id' => $recipe->rawMaterial->id,
                                'name' =>
                                    $recipe->rawMaterial->name,
                                'unit_measurement' =>
                                    $recipe->rawMaterial->unit_measurement,
                                'current_stock' =>
                                    $recipe->rawMaterial->current_stock,
                            ]
                            : null,
                        'amount_needed' =>
                            $recipe->amount_needed,
                    ];
                })
                ->values(),

            'packagings' => $product->packagings
                ->map(function ($packaging) {
                    return [
                        'id' => $packaging->id,
                        'purchase_unit' =>
                            $packaging->purchase_unit,
                        'conversion_factor' =>
                            $packaging->conversion_factor,
                        'is_active' =>
                            $packaging->is_active,
                    ];
                })
                ->values(),

            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }

    private function calculateStock(Product $product): ?int
    {
        /*
         * UNTRACKED
         */
        if ($product->stock_type === 'untracked') {
            return null;
        }

        if ($product->stock_type === 'static') {
            return (int) $product->stock;
        }

        $recipes = $product->recipes;

        if ($recipes->isEmpty()) {
            return 0;
        }

        $possibleStock = $recipes->map(function ($recipe) {

            $rawMaterial = $recipe->rawMaterial;

            if (!$rawMaterial) {
                return 0;
            }

            $currentStock = (float) $rawMaterial->current_stock;
            $amountNeeded = (float) $recipe->amount_needed;

            if ($amountNeeded <= 0) {
                return 0;
            }

            return (int) floor(
                $currentStock / $amountNeeded
            );
        });

        return $possibleStock->min() ?? 0;
    }
}
