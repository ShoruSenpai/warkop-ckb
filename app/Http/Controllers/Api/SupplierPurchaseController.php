<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPurchaseController extends Controller
{
    public function getInventoryItems()
    {
        $rawMaterials = RawMaterial::select('id', 'name', 'unit_measurement')
            ->get()
            ->map(function ($item) {
                return [
                    'item_key' => 'RAW-' . $item->id,
                    'raw_material_id' => $item->id,
                    'product_id' => null,
                    'name' => '[Bahan Baku] ' . $item->name,
                    'type' => 'raw_material',
                    'base_unit' => $item->unit_measurement,
                ];
            });

        $product = Product::where('stock_type', 'static')
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                return [
                    'item_key' => 'PROD-' . $item->id,
                    'raw_material_id' => null,
                    'product_id' => $item->id,
                    'name' => '[Barang Eceran] ' . $item->name,
                    'type' => 'product',
                    'base_unit' => 'pcs'
                ];
            });

        $merged = $rawMaterials->concat($product);

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
            ->filter();

        return response()->json([
            'success' => true,
            'message' => 'Get Supplier Suggestion',
            'data' => $supplier->value(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:supplier_purchases,invoice_number',
            'supplier_name' => 'required|string',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'nullable|exists:raw_materials,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.purchase_unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.conversion_factor' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $purchase = SupplierPurchase::create([
                'invoice_number' => $request->invoice_number,
                'supplier_name' => $request->supplier_name,
                'total_cost' => $totalCost,
                'purchase_date' => $request->purchase_date,
                'status' => $request->status,
            ]);

            foreach ($request->items as $index => $item) {
                $hasRaw = !empty($item['raw_material_id']);
                $hasProd = !empty($item['product_id']);

                if (!$hasRaw && !$hasProd) {
                    throw new \Exception("Item baris ke-" . ($index + 1) . " harus terhubung ke Bahan Baku atau Produk.");
                }
                if ($hasRaw && $hasProd) {
                    throw new \Exception("Item baris ke-" . ($index + 1) . " tidak boleh memilih Bahan Baku dan Produk sekaligus.");
                }

                $subtotal = $item['quantity'] * $item['unit_price'];

                SupplierPurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $item['raw_material_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'purchase_unit' => $item['purchase_unit'],
                    'quantity' => $item['quantity'],
                    'conversion_factor' => $item['conversion_factor'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                if ($purchase->status === 'received') {
                    $stockToAdd = $item['quantity'] * $item['conversion_factor'];

                    if ($hasRaw) {
                        RawMaterial::where('id', $item['raw_material_id'])->increment('current_stock', $stockToAdd);
                    } elseif ($hasProd) {
                        Product::where('id', $item['product_id'])->increment('stock', $stockToAdd);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchases successfully created & updated stock.',
                'data' => $purchase->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembelian: ' . $e->getMessage()
            ], 400);
        }
    }
}
