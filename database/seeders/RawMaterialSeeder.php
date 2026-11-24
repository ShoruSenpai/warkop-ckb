<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawMaterialSeeder extends Seeder
{
    /**
     * Stock is initialized to zero only when a material is first created.
     * Re-running this seeder does not overwrite live/demo stock quantities.
     */
    public function run(): void
    {
        $materials = [
            ['name' => 'Biji Kopi', 'unit' => 'gram', 'minimum_stock' => 1000, 'packaging' => ['kg' => 1000]],
            ['name' => 'Susu UHT', 'unit' => 'ml', 'minimum_stock' => 2000, 'packaging' => ['karton' => 1000]],
            ['name' => 'Gula Aren', 'unit' => 'gram', 'minimum_stock' => 500, 'packaging' => ['kg' => 1000]],
            ['name' => 'Gula Pasir', 'unit' => 'gram', 'minimum_stock' => 500, 'packaging' => ['kg' => 1000]],
            ['name' => 'Bubuk Matcha', 'unit' => 'gram', 'minimum_stock' => 100, 'packaging' => ['pack 500g' => 500]],
            ['name' => 'Bubuk Cokelat', 'unit' => 'gram', 'minimum_stock' => 300, 'packaging' => ['kg' => 1000]],
            ['name' => 'Air Mineral', 'unit' => 'ml', 'minimum_stock' => 3000, 'packaging' => ['galon' => 19000]],
            ['name' => 'Es Batu', 'unit' => 'gram', 'minimum_stock' => 2000, 'packaging' => ['kg' => 1000]],
            ['name' => 'Mie Instan', 'unit' => 'pcs', 'minimum_stock' => 5, 'packaging' => ['pcs' => 1]],
            ['name' => 'Telur Ayam', 'unit' => 'pcs', 'minimum_stock' => 5, 'packaging' => ['tray' => 30]],
            ['name' => 'Minyak Goreng', 'unit' => 'ml', 'minimum_stock' => 1000, 'packaging' => ['liter' => 1000]],
            ['name' => 'Beras', 'unit' => 'gram', 'minimum_stock' => 1000, 'packaging' => ['kg' => 1000]],
            ['name' => 'Bumbu Nasi Goreng', 'unit' => 'gram', 'minimum_stock' => 200, 'packaging' => ['pack 1kg' => 1000]],
            ['name' => 'Kentang Frozen', 'unit' => 'gram', 'minimum_stock' => 1000, 'packaging' => ['kg' => 1000]],
            ['name' => 'Roti Tawar', 'unit' => 'slice', 'minimum_stock' => 8, 'packaging' => ['pack' => 20]],
            ['name' => 'Margarin', 'unit' => 'gram', 'minimum_stock' => 200, 'packaging' => ['tub 200g' => 200]],
        ];

        DB::transaction(function () use ($materials): void {
            foreach ($materials as $material) {
                $now = now();
                $existing = DB::table('raw_materials')
                    ->where('name', $material['name'])
                    ->first();

                if ($existing) {
                    $materialId = (int) $existing->id;
                    DB::table('raw_materials')->where('id', $materialId)->update([
                        'unit_measurement' => $material['unit'],
                        'minimum_stock' => $material['minimum_stock'],
                        'updated_at' => $now,
                    ]);
                } else {
                    $materialId = (int) DB::table('raw_materials')->insertGetId([
                        'name' => $material['name'],
                        'unit_measurement' => $material['unit'],
                        'current_stock' => 0,
                        'minimum_stock' => $material['minimum_stock'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($material['packaging'] as $purchaseUnit => $conversionFactor) {
                    $packaging = DB::table('raw_material_packagings')
                        ->where('raw_material_id', $materialId)
                        ->whereRaw('lower(btrim(purchase_unit)) = ?', [mb_strtolower(trim($purchaseUnit))])
                        ->first();

                    $packagingData = [
                        'raw_material_id' => $materialId,
                        'purchase_unit' => $purchaseUnit,
                        'conversion_factor' => $conversionFactor,
                        'is_active' => true,
                        'updated_at' => $now,
                    ];

                    if ($packaging) {
                        DB::table('raw_material_packagings')
                            ->where('id', $packaging->id)
                            ->update($packagingData);
                    } else {
                        $packagingData['created_at'] = $now;
                        DB::table('raw_material_packagings')->insert($packagingData);
                    }
                }
            }
        });
    }
}
