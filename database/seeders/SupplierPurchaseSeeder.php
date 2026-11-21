<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierPurchaseSeeder extends Seeder
{
    /**
     * Seed one opening-stock invoice. The invoice guard and stock update are
     * inside one transaction, so running db:seed again will not add stock twice.
     */
    public function run(): void
    {
        $invoiceNumber = "SEED-OPENING-001";

        if (
            DB::table("supplier_purchases")
                ->where("invoice_number", $invoiceNumber)
                ->exists()
        ) {
            return;
        }

        $lines = [
            [
                "name" => "Biji Kopi",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 5,
                "factor" => 1000,
                "unit_price" => 120000,
            ],
            [
                "name" => "Susu UHT",
                "unit" => "karton",
                "packaging_snapshot" => "Karton 1 liter",
                "quantity" => 12,
                "factor" => 1000,
                "unit_price" => 18000,
            ],
            [
                "name" => "Gula Aren",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 3,
                "factor" => 1000,
                "unit_price" => 35000,
            ],
            [
                "name" => "Gula Pasir",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 3,
                "factor" => 1000,
                "unit_price" => 16000,
            ],
            [
                "name" => "Bubuk Matcha",
                "unit" => "pack 500g",
                "packaging_snapshot" => "Kemasan 500 gram",
                "quantity" => 1,
                "factor" => 500,
                "unit_price" => 125000,
            ],
            [
                "name" => "Bubuk Cokelat",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 2,
                "factor" => 1000,
                "unit_price" => 70000,
            ],
            [
                "name" => "Air Mineral",
                "unit" => "galon",
                "packaging_snapshot" => "Galon 19 liter",
                "quantity" => 1,
                "factor" => 19000,
                "unit_price" => 20000,
            ],
            [
                "name" => "Es Batu",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 10,
                "factor" => 1000,
                "unit_price" => 3000,
            ],
            [
                "name" => "Mie Instan",
                "unit" => "pcs",
                "packaging_snapshot" => "Satuan 1 pcs",
                "quantity" => 30,
                "factor" => 1,
                "unit_price" => 3000,
            ],
            [
                "name" => "Telur Ayam",
                "unit" => "tray",
                "packaging_snapshot" => "Tray 30 butir",
                "quantity" => 1,
                "factor" => 30,
                "unit_price" => 60000,
            ],
            [
                "name" => "Minyak Goreng",
                "unit" => "liter",
                "packaging_snapshot" => "Botol 1 liter",
                "quantity" => 5,
                "factor" => 1000,
                "unit_price" => 18000,
            ],
            [
                "name" => "Beras",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 5,
                "factor" => 1000,
                "unit_price" => 15000,
            ],
            [
                "name" => "Bumbu Nasi Goreng",
                "unit" => "pack 1kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 1,
                "factor" => 1000,
                "unit_price" => 12000,
            ],
            [
                "name" => "Kentang Frozen",
                "unit" => "kg",
                "packaging_snapshot" => "Kemasan 1 kg",
                "quantity" => 5,
                "factor" => 1000,
                "unit_price" => 28000,
            ],
            [
                "name" => "Roti Tawar",
                "unit" => "pack",
                "packaging_snapshot" => "Kemasan 20 slice",
                "quantity" => 2,
                "factor" => 20,
                "unit_price" => 18000,
            ],
            [
                "name" => "Margarin",
                "unit" => "tub 200g",
                "packaging_snapshot" => "Kemasan 200 gram",
                "quantity" => 5,
                "factor" => 200,
                "unit_price" => 8000,
            ],
        ];

        DB::transaction(function () use ($invoiceNumber, $lines): void {
            $now = now();
            $totalCost = array_sum(
                array_map(
                    fn(array $line): int => $line["quantity"] *
                        $line["unit_price"],
                    $lines,
                ),
            );

            $purchaseId = (int) DB::table("supplier_purchases")->insertGetId([
                "invoice_number" => $invoiceNumber,
                "supplier_name" => "Supplier Demo",
                "total_cost" => $totalCost,
                "purchase_date" => now(
                    config("business.timezone", "Asia/Jakarta"),
                )->toDateString(),
                // Confirm this status matches the application's purchase workflow.
                "status" => config(
                    "seeding.opening_purchase_status",
                    "received",
                ),
                "created_at" => $now,
                "updated_at" => $now,
            ]);

            foreach ($lines as $line) {
                $material = DB::table("raw_materials")
                    ->where("name", $line["name"])
                    ->first();

                if (!$material) {
                    throw new RuntimeException(
                        "Raw material {$line["name"]} was not seeded.",
                    );
                }

                $packaging = DB::table("raw_material_packagings")
                    ->where("raw_material_id", $material->id)
                    ->where("purchase_unit", $line["unit"])
                    ->first();

                if (!$packaging) {
                    throw new RuntimeException(
                        "Packaging {$line["unit"]} for {$line["name"]} was not seeded.",
                    );
                }

                $subtotal = $line["quantity"] * $line["unit_price"];

                DB::table("supplier_purchase_items")->insert([
                    "purchase_id" => $purchaseId,
                    "item_type" => "raw_material",
                    "raw_material_id" => $material->id,
                    "raw_material_packaging_id" => $packaging->id,
                    "product_id" => null,
                    "product_packaging_id" => null,
                    "item_name_snapshot" => $line["name"],
                    "packaging_name_snapshot" => $line["packaging_snapshot"],
                    "purchase_unit" => $line["unit"],
                    "quantity" => $line["quantity"],
                    "conversion_factor" => $line["factor"],
                    "unit_price" => $line["unit_price"],
                    "subtotal" => $subtotal,
                ]);

                // Stock is stored in each material's base unit (gram, ml, pcs, slice).
                $stockIncrease = $line["quantity"] * $line["factor"];
                DB::table("raw_materials")
                    ->where("id", $material->id)
                    ->increment("current_stock", $stockIncrease, [
                        "updated_at" => $now,
                    ]);
            }
        });
    }
}
