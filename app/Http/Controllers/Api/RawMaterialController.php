<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\RawMaterialPackaging;
use App\Models\SupplierPurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'List all raw materials',
            'data' => $materials,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'unit_measurement' => 'required|in:gram,ml,pcs',
            ],
            [
                'name.required' => 'Nama bahan baku wajib diisi.',
                'name.max' => 'Nama bahan baku maksimal 100 karakter.',
                'unit_measurement.required' => 'Satuan dasar wajib dipilih.',
                'unit_measurement.in' => 'Satuan dasar harus gram, ml, atau pcs.',
            ]
        );

        $rawMaterial = RawMaterial::create([
            'name' => $validated['name'],
            'unit_measurement' => $validated['unit_measurement'],
            'current_stock' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil ditambahkan.',
            'data' => $rawMaterial,
        ], 201);
    }


    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'unit_measurement' => 'required|in:gram,ml,pcs',
            ],
            [
                'name.required' => 'Nama bahan baku wajib diisi.',
                'name.max' => 'Nama bahan baku maksimal 100 karakter.',
                'unit_measurement.required' => 'Satuan dasar wajib dipilih.',
                'unit_measurement.in' => 'Satuan dasar harus gram, ml, atau pcs.',
            ]
        );

        if (
            $rawMaterial->unit_measurement !== $validated['unit_measurement']
            && (float) $rawMaterial->current_stock > 0
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan dasar tidak dapat diubah karena bahan baku masih memiliki stok. Habiskan stok terlebih dahulu sebelum mengubah satuan.',
            ], 422);
        }

        $hasPurchaseHistory = SupplierPurchaseItem::where(
            'raw_material_id',
            $rawMaterial->id
        )->exists();

        if (
            $hasPurchaseHistory
            && $rawMaterial->unit_measurement !== $validated['unit_measurement']
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan dasar tidak dapat diubah karena bahan baku sudah memiliki riwayat pembelian.',
            ], 422);
        }

        $rawMaterial->update([
            'name' => $validated['name'],
            'unit_measurement' => $validated['unit_measurement'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil diperbarui.',
            'data' => $rawMaterial->fresh(),
        ]);
    }


    public function destroy(RawMaterial $rawMaterial)
    {
        $hasPurchaseHistory = SupplierPurchaseItem::where(
            'raw_material_id',
            $rawMaterial->id
        )->exists();

        if ($hasPurchaseHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan baku tidak dapat dihapus karena sudah memiliki riwayat pembelian.',
            ], 422);
        }

        if ($rawMaterial->recipes()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan baku tidak dapat dihapus karena masih digunakan dalam resep produk.',
            ], 422);
        }

        $rawMaterial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil dihapus.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RAW MATERIAL PACKAGING
    |--------------------------------------------------------------------------
    */

    public function packagingIndex(RawMaterial $rawMaterial)
    {
        $packagings = $rawMaterial
            ->packagings()
            ->orderBy('purchase_unit')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List raw material packagings',
            'data' => $packagings,
        ]);
    }


    public function packagingStore(
        Request $request,
        RawMaterial $rawMaterial
    ) {
        $validated = $request->validate(
            [
                'purchase_unit' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('raw_material_packagings', 'purchase_unit')
                        ->where(
                            fn ($query) =>
                            $query->where(
                                'raw_material_id',
                                $rawMaterial->id
                            )
                        ),
                ],

                'conversion_factor' => 'required|numeric|min:0.001',

                'is_active' => 'nullable|boolean',
            ],
            [
                'purchase_unit.required' =>
                    'Satuan beli wajib diisi.',

                'purchase_unit.max' =>
                    'Satuan beli maksimal 20 karakter.',

                'purchase_unit.unique' =>
                    'Satuan beli tersebut sudah terdaftar untuk bahan baku ini.',

                'conversion_factor.required' =>
                    'Nilai konversi wajib diisi.',

                'conversion_factor.numeric' =>
                    'Nilai konversi harus berupa angka.',

                'conversion_factor.min' =>
                    'Nilai konversi harus lebih besar dari 0.',
            ]
        );

        $packaging = $rawMaterial
            ->packagings()
            ->create([
                'purchase_unit' => $validated['purchase_unit'],
                'conversion_factor' => $validated['conversion_factor'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Kemasan berhasil ditambahkan.',
            'data' => $packaging,
        ], 201);
    }


    public function packagingUpdate(
        Request $request,
        RawMaterial $rawMaterial,
        RawMaterialPackaging $packaging
    ) {
        /*
         * Pastikan packaging memang milik
         * raw material yang sedang dibuka.
         */
        if ($packaging->raw_material_id !== $rawMaterial->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kemasan tidak ditemukan pada bahan baku tersebut.',
            ], 404);
        }

        $validated = $request->validate(
            [
                'purchase_unit' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('raw_material_packagings', 'purchase_unit')
                        ->where(
                            fn ($query) =>
                            $query->where(
                                'raw_material_id',
                                $rawMaterial->id
                            )
                        )
                        ->ignore($packaging->id),
                ],

                'conversion_factor' => 'required|numeric|min:0.001',

                'is_active' => 'nullable|boolean',
            ],
            [
                'purchase_unit.required' =>
                    'Satuan beli wajib diisi.',

                'purchase_unit.max' =>
                    'Satuan beli maksimal 20 karakter.',

                'purchase_unit.unique' =>
                    'Satuan beli tersebut sudah terdaftar untuk bahan baku ini.',

                'conversion_factor.required' =>
                    'Nilai konversi wajib diisi.',

                'conversion_factor.numeric' =>
                    'Nilai konversi harus berupa angka.',

                'conversion_factor.min' =>
                    'Nilai konversi harus lebih besar dari 0.',
            ]
        );

        $packaging->update([
            'purchase_unit' => $validated['purchase_unit'],
            'conversion_factor' => $validated['conversion_factor'],
            'is_active' => $validated['is_active'] ?? $packaging->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kemasan berhasil diperbarui.',
            'data' => $packaging->fresh(),
        ]);
    }


    public function packagingDestroy(
        RawMaterial $rawMaterial,
        RawMaterialPackaging $packaging
    ) {
        if ($packaging->raw_material_id !== $rawMaterial->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kemasan tidak ditemukan pada bahan baku tersebut.',
            ], 404);
        }

        $used = SupplierPurchaseItem::where(
            'raw_material_packaging_id',
            $packaging->id
        )->exists();

        if ($used) {
            return response()->json([
                'success' => false,
                'message' => 'Kemasan tidak dapat dihapus karena sudah digunakan pada transaksi pembelian. Nonaktifkan kemasan tersebut agar tidak muncul pada pembelian berikutnya.',
            ], 422);
        }

        $packaging->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kemasan berhasil dihapus.',
        ]);
    }
}
