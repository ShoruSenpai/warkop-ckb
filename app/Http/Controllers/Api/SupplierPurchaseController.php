<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductPackaging;
use App\Models\RawMaterial;
use App\Models\RawMaterialPackaging;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPurchaseController extends Controller
{
    public function getInventoryItems()
    {
        $rawMaterials = RawMaterial::with([
            'packagings' => function ($query) {
            $query
                ->where('is_active', 'true')
                ->select(
                    'id',
                    'raw_material_id',
                    'purchase_unit',
                    'conversion_factor'
                );
            }
        ])
            ->select('id', 'name', 'unit_measurement')
            ->orderBy('name', )
            ->get()
            ->map(function ($item) {
                return [
                  'item_key' => 'RAW-' . $item->id,
                    'raw_material_id' => $item->id,
                    'product_id' => null,
                    'name' => '[Bahan Baku] ' . $item->name,
                    'type' => 'raw_material',
                    'base_unit' => $item->unit_measurement,

                    'packagings' => $item->getRelation('packagings')
                        ->map(function ($packaging) {
                            return [
                              'id' => $packaging->id,
                                'purchase_unit' => $packaging->purchase_unit,
                                'conversion_factor' => $packaging->conversion_factor,
                            ];
                        })
                            ->values()
                ];
            });

        $products = Product::with([
            'packagings' => function ($query) {
                $query
                    ->where('is_active', 'true')
                    ->select(
                        'id',
                        'product_id',
                        'purchase_unit',
                        'conversion_factor'
                    );
            }
        ])
            ->where('stock_type', 'static')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'item_key' => 'PROD-' . $item->id,
                    'raw_material_id' => null,
                    'product_id' => $item->id,
                    'name' => '[Barang Eceran] ' . $item->name,
                    'type' => 'product',
                    'base_unit' => 'pcs',

                    'packagings' => $item->packagings
                        ->map(function ($packaging) {
                            return [
                                'id' => $packaging->id,
                                'purchase_unit' => $packaging->purchase_unit,
                                'conversion_factor' => $packaging->conversion_factor,
                            ];
                        })
                    ->values()
                ];
            });

        $merged = $rawMaterials->concat($products)->values();

        return response()->json([
            'success' => true,
            'message' => 'Get Inventory Items',
            'data' => $merged,
        ]);
    }

    public function getSupplierSuggestions()
    {
        $supplier = SupplierPurchase::distinct()
            ->pluck('supplier_name')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Get Supplier Suggestion',
            'data' => $supplier,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:supplier_purchases,invoice_number',
            'supplier_name' => 'required|string',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received',

            'items' => 'required|array|min:1',

            'items.*.raw_material_id' => 'nullable|integer|exists:raw_materials,id',
            'items.*.raw_material_packaging_id' => 'nullable|integer|exists:raw_material_packagings,id',

            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.product_packaging_id' => 'nullable|integer|exists:product_packagings,id',

            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $preparedItems = [];
            $totalCost = 0;

            foreach ($validated['items'] as $index => $item) {

                $hasRawMaterial = !empty($item['raw_material_id']);
                $hasProduct = !empty($item['product_id']);

                $hasRawPackaging = !empty($item['raw_material_packaging_id']);
                $hasProductPackaging = !empty($item['product_packaging_id']);

                if ($hasRawMaterial === $hasProduct) {
                    throw new \Exception(
                        "Item baris ke-" . ($index + 1) .
                        " harus memilih tepat satu: Bahan Baku atau Produk."
                    );
                }

                $packaging = null;

                if ($hasRawMaterial) {

                    if (!$hasRawPackaging || $hasProductPackaging) {
                        throw new \Exception(
                            "Item baris ke-" . ($index + 1) .
                            " untuk Bahan Baku harus memiliki raw_material_packaging_id."
                        );
                    }

                    $packaging = RawMaterialPackaging::query()
                        ->where('id', $item['raw_material_packaging_id'])
                        ->where('raw_material_id', $item['raw_material_id'])
                        ->where('is_active', true)
                        ->first();

                    if (!$packaging) {
                        throw new \Exception(
                            "Packaging Bahan Baku pada baris ke-" .
                            ($index + 1) .
                            " tidak valid atau sudah tidak aktif."
                        );
                    }

                } elseif ($hasProduct) {

                    if (!$hasProductPackaging || $hasRawPackaging) {
                        throw new \Exception(
                            "Item baris ke-" . ($index + 1) .
                            " untuk Produk harus memiliki product_packaging_id."
                        );
                    }

                    $product = Product::find($item['product_id']);

                    if (!$product) {
                        throw new \Exception(
                            "Produk pada baris ke-" . ($index + 1) .
                            " tidak ditemukan."
                        );
                    }

                    if ($product->stock_type !== 'static') {
                        throw new \Exception(
                            "Produk '" . $product->name .
                            "' tidak menggunakan stok static."
                        );
                    }

                    $packaging = ProductPackaging::query()
                        ->where('id', $item['product_packaging_id'])
                        ->where('product_id', $item['product_id'])
                        ->where('is_active', true)
                        ->first();

                    if (!$packaging) {
                        throw new \Exception(
                            "Packaging Produk pada baris ke-" .
                            ($index + 1) .
                            " tidak valid atau sudah tidak aktif."
                        );
                    }
                }

                $purchaseUnit = $packaging->purchase_unit;
                $conversionFactor = (float) $packaging->conversion_factor;

                if ($conversionFactor <= 0) {
                    throw new \Exception(
                        "Conversion factor pada baris ke-" .
                        ($index + 1) .
                        " tidak valid."
                    );
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $subtotal = round($quantity * $unitPrice, 2);

                $totalCost = round($totalCost + $subtotal, 2);

                $preparedItems[] = [
                    'raw_material_id' => $item['raw_material_id'] ?? null,
                    'raw_material_packaging_id' =>
                        $item['raw_material_packaging_id'] ?? null,

                    'product_id' => $item['product_id'] ?? null,
                    'product_packaging_id' =>
                        $item['product_packaging_id'] ?? null,

                    'purchase_unit' => $purchaseUnit,
                    'quantity' => $quantity,
                    'conversion_factor' => $conversionFactor,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $purchase = SupplierPurchase::create([
                'invoice_number' => $validated['invoice_number'],
                'supplier_name' => $validated['supplier_name'],
                'total_cost' => $totalCost,
                'purchase_date' => $validated['purchase_date'],
                'status' => $validated['status'],
            ]);

            foreach ($preparedItems as $item) {

                SupplierPurchaseItem::create([
                    'purchase_id' => $purchase->id,

                    'raw_material_id' => $item['raw_material_id'],
                    'raw_material_packaging_id' =>
                        $item['raw_material_packaging_id'],

                    'product_id' => $item['product_id'],
                    'product_packaging_id' =>
                        $item['product_packaging_id'],

                    'purchase_unit' => $item['purchase_unit'],
                    'quantity' => $item['quantity'],
                    'conversion_factor' =>
                        $item['conversion_factor'],

                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                if ($purchase->status === 'received') {

                    $stockToAdd =
                        $item['quantity'] *
                        $item['conversion_factor'];

                    if ($item['raw_material_id']) {

                        RawMaterial::where(
                            'id',
                            $item['raw_material_id']
                        )->increment(
                            'current_stock',
                            $stockToAdd
                        );

                    } elseif ($item['product_id']) {

                        Product::where(
                            'id',
                            $item['product_id']
                        )->increment(
                            'stock',
                            $stockToAdd
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchases successfully created & updated stock.',
                'data' => $purchase->load('items'),
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembelian: ' .
                    $e->getMessage(),
            ], 400);
        }
    }
}
