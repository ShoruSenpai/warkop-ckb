<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductSeeder extends Seeder
{
    /**
     * Demo menu catalog with category, recipe, packaging, and option data.
     * Existing product stock is deliberately preserved on repeated seeding.
     */
    public function run(): void
    {
        $products = $this->products();

        DB::transaction(function () use ($products): void {
            foreach ($products as $definition) {
                $category = DB::table("categories")
                    ->where("name", $definition["category"])
                    ->first();

                if (!$category) {
                    throw new RuntimeException(
                        "Category {$definition["category"]} was not seeded.",
                    );
                }

                $productId = $this->upsertProduct(
                    (int) $category->id,
                    $definition,
                );
                $this->syncRecipeRows($productId, $definition["recipes"] ?? []);
                $this->syncOptionGroups(
                    $productId,
                    $definition["options"] ?? [],
                );
                $this->syncProductPackagings(
                    $productId,
                    $definition["packagings"] ?? [],
                );
            }
        });
    }

    private function products(): array
    {
        $drinkOptions = [
            [
                "group_name" => "Ukuran",
                "is_required" => true,
                "max_choices" => 1,
                "values" => [
                    ["name" => "Regular", "extra_price" => 0],
                    ["name" => "Large", "extra_price" => 5000],
                ],
            ],
            [
                "group_name" => "Level Gula",
                "is_required" => false,
                "max_choices" => 1,
                "values" => [
                    ["name" => "Normal", "extra_price" => 0],
                    ["name" => "Less Sugar", "extra_price" => 0],
                    ["name" => "No Sugar", "extra_price" => 0],
                ],
            ],
            [
                "group_name" => "Es",
                "is_required" => false,
                "max_choices" => 1,
                "values" => [
                    ["name" => "Normal Ice", "extra_price" => 0],
                    ["name" => "Less Ice", "extra_price" => 0],
                    ["name" => "No Ice", "extra_price" => 0],
                ],
            ],
        ];

        return [
            // COFFEE
            [
                "name" => "Es Kopi Susu",
                "category" => "Coffee",
                "description" => "Kopi susu dengan gula aren.",
                "base_price" => 15000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => true,
                "recipes" => [
                    "Biji Kopi" => 18,
                    "Susu UHT" => 120,
                    "Gula Aren" => 20,
                    "Air Mineral" => 30,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],
            [
                "name" => "Americano",
                "category" => "Coffee",
                "description" => "Espresso dengan air mineral.",
                "base_price" => 12000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Biji Kopi" => 18,
                    "Air Mineral" => 180,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],

            // NON COFFEE
            [
                "name" => "Matcha Latte",
                "category" => "Non Coffee",
                "description" => "Minuman matcha dengan susu.",
                "base_price" => 18000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => true,
                "recipes" => [
                    "Bubuk Matcha" => 8,
                    "Susu UHT" => 180,
                    "Gula Pasir" => 10,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],
            [
                "name" => "Chocolate Milk",
                "category" => "Non Coffee",
                "description" => "Minuman cokelat dengan susu.",
                "base_price" => 16000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Bubuk Cokelat" => 25,
                    "Susu UHT" => 180,
                    "Gula Pasir" => 10,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],

            // BASE MILK
            [
                "name" => "Fresh Milk",
                "category" => "Base Milk",
                "description" => "Susu UHT dingin.",
                "base_price" => 10000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Susu UHT" => 200,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],
            [
                "name" => "Susu Gula Aren",
                "category" => "Base Milk",
                "description" => "Susu dingin dengan gula aren.",
                "base_price" => 14000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Susu UHT" => 180,
                    "Gula Aren" => 20,
                    "Es Batu" => 150,
                ],
                "options" => $drinkOptions,
            ],

            // FOOD
            [
                "name" => "Mie Goreng Telur",
                "category" => "Food",
                "description" => "Mie goreng dengan telur.",
                "base_price" => 15000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => true,
                "recipes" => [
                    "Mie Instan" => 1,
                    "Telur Ayam" => 1,
                    "Minyak Goreng" => 10,
                    "Air Mineral" => 200,
                ],
                "options" => [
                    [
                        "group_name" => "Level Pedas",
                        "is_required" => true,
                        "max_choices" => 1,
                        "values" => [
                            ["name" => "Tidak Pedas", "extra_price" => 0],
                            ["name" => "Sedang", "extra_price" => 0],
                            ["name" => "Pedas", "extra_price" => 0],
                        ],
                    ],
                ],
            ],
            [
                "name" => "Nasi Goreng Telur",
                "category" => "Food",
                "description" => "Nasi goreng dengan telur.",
                "base_price" => 18000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Beras" => 75,
                    "Telur Ayam" => 1,
                    "Minyak Goreng" => 15,
                    "Bumbu Nasi Goreng" => 10,
                ],
                "options" => [
                    [
                        "group_name" => "Level Pedas",
                        "is_required" => true,
                        "max_choices" => 1,
                        "values" => [
                            ["name" => "Tidak Pedas", "extra_price" => 0],
                            ["name" => "Sedang", "extra_price" => 0],
                            ["name" => "Pedas", "extra_price" => 0],
                        ],
                    ],
                ],
            ],

            // SNACK
            [
                "name" => "French Fries",
                "category" => "Snack",
                "description" => "Kentang goreng dengan pilihan bumbu.",
                "base_price" => 12000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Kentang Frozen" => 150,
                    "Minyak Goreng" => 15,
                ],
                "options" => [
                    [
                        "group_name" => "Bumbu",
                        "is_required" => true,
                        "max_choices" => 1,
                        "values" => [
                            ["name" => "Original", "extra_price" => 0],
                            ["name" => "BBQ", "extra_price" => 0],
                            ["name" => "Balado", "extra_price" => 0],
                        ],
                    ],
                ],
            ],
            [
                "name" => "Roti Bakar Cokelat",
                "category" => "Snack",
                "description" => "Roti bakar dengan cokelat dan margarin.",
                "base_price" => 13000,
                "stock_type" => "recipe",
                "stock" => 0,
                "is_recommended" => false,
                "recipes" => [
                    "Roti Tawar" => 2,
                    "Margarin" => 10,
                    "Bubuk Cokelat" => 15,
                    "Gula Pasir" => 5,
                ],
            ],

            // CIGARETTES: static stock, not recipe-based.
            [
                "name" => "Rokok Filter 12",
                "category" => "Cigarette",
                "description" =>
                    "Produk demo. Sesuaikan nama, harga, dan stok dengan barang toko.",
                "base_price" => 25000,
                "stock_type" => "static",
                "stock" => 20,
                "is_recommended" => false,
                "recipes" => [],
                "packagings" => [
                    ["purchase_unit" => "slop", "conversion_factor" => 10],
                ],
            ],
            [
                "name" => "Rokok Kretek 12",
                "category" => "Cigarette",
                "description" =>
                    "Produk demo. Sesuaikan nama, harga, dan stok dengan barang toko.",
                "base_price" => 22000,
                "stock_type" => "static",
                "stock" => 20,
                "is_recommended" => false,
                "recipes" => [],
                "packagings" => [
                    ["purchase_unit" => "slop", "conversion_factor" => 10],
                ],
            ],
        ];
    }

    private function upsertProduct(int $categoryId, array $definition): int
    {
        $now = now();
        $existing = DB::table("products")
            ->where("name", $definition["name"])
            ->first();

        $data = [
            "category_id" => $categoryId,
            "name" => $definition["name"],
            "description" => $definition["description"] ?? null,
            "base_price" => $definition["base_price"],
            "stock_type" => $definition["stock_type"],
            "is_recommended" => $definition["is_recommended"],
            "status" => "available",
            "updated_at" => $now,
        ];

        if ($existing) {
            // Do not reset stock on an existing product when seeding is repeated.
            DB::table("products")->where("id", $existing->id)->update($data);
            return (int) $existing->id;
        }

        $data["stock"] = $definition["stock"];
        $data["created_at"] = $now;

        return (int) DB::table("products")->insertGetId($data);
    }

    private function syncRecipeRows(int $productId, array $recipes): void
    {
        foreach ($recipes as $materialName => $amountNeeded) {
            $material = DB::table("raw_materials")
                ->where("name", $materialName)
                ->first();

            if (!$material) {
                throw new RuntimeException(
                    "Raw material {$materialName} was not seeded.",
                );
            }

            DB::table("product_recipes")->updateOrInsert(
                [
                    "product_id" => $productId,
                    "raw_material_id" => $material->id,
                ],
                ["amount_needed" => $amountNeeded],
            );
        }
    }

    private function syncOptionGroups(int $productId, array $groups): void
    {
        foreach ($groups as $group) {
            $existingGroup = DB::table("product_option_groups")
                ->where("product_id", $productId)
                ->where("group_name", $group["group_name"])
                ->first();

            $groupData = [
                "product_id" => $productId,
                "group_name" => $group["group_name"],
                "is_required" => $group["is_required"],
                "max_choices" => $group["max_choices"],
            ];

            if ($existingGroup) {
                DB::table("product_option_groups")
                    ->where("id", $existingGroup->id)
                    ->update($groupData);
                $groupId = (int) $existingGroup->id;
            } else {
                $groupId = (int) DB::table(
                    "product_option_groups",
                )->insertGetId($groupData);
            }

            foreach ($group["values"] as $value) {
                $existingValue = DB::table("product_option_values")
                    ->where("option_group_id", $groupId)
                    ->where("option_name", $value["name"])
                    ->first();

                $valueData = [
                    "option_group_id" => $groupId,
                    "option_name" => $value["name"],
                    "extra_price" => $value["extra_price"],
                ];

                if ($existingValue) {
                    DB::table("product_option_values")
                        ->where("id", $existingValue->id)
                        ->update($valueData);
                } else {
                    DB::table("product_option_values")->insert($valueData);
                }
            }
        }
    }

    private function syncProductPackagings(
        int $productId,
        array $packagings,
    ): void {
        foreach ($packagings as $packaging) {
            $existing = DB::table("product_packagings")
                ->where("product_id", $productId)
                ->whereRaw("lower(btrim(purchase_unit)) = ?", [
                    mb_strtolower(trim($packaging["purchase_unit"])),
                ])
                ->first();

            $now = now();
            $data = [
                "product_id" => $productId,
                "purchase_unit" => $packaging["purchase_unit"],
                "conversion_factor" => $packaging["conversion_factor"],
                "is_active" => true,
                "updated_at" => $now,
            ];

            if ($existing) {
                DB::table("product_packagings")
                    ->where("id", $existing->id)
                    ->update($data);
            } else {
                $data["created_at"] = $now;
                DB::table("product_packagings")->insert($data);
            }
        }
    }
}
