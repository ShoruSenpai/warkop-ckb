<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("supplier_purchase_items", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("purchase_id")
                ->constrained("supplier_purchases")
                ->cascadeOnDelete();
            $table
                ->foreignId("raw_material_id")
                ->nullable()
                ->constrained("raw_materials")
                ->nullOnDelete();
            $table
                ->foreignId("raw_material_packaging_id")
                ->nullable()
                ->constrained("raw_material_packagings")
                ->nullOnDelete();
            $table
                ->foreignId("product_id")
                ->nullable()
                ->constrained("products")
                ->nullOnDelete();
            $table
                ->foreignId("product_packaging_id")
                ->nullable()
                ->constrained("product_packagings")
                ->nullOnDelete();
            $table->string("purchase_unit", 50);
            $table->bigInteger("quantity");
            $table->bigInteger("conversion_factor");
            $table->bigInteger("unit_price");
            $table->bigInteger("subtotal");

            $table->index("purchase_id", "idx_purchase_items_purchase");
            $table->index("raw_material_id", "idx_purchase_items_raw_material");
            $table->index("product_id", "idx_purchase_items_product");
            $table->index(
                "raw_material_packaging_id",
                "idx_purchase_items_raw_packaging",
            );
            $table->index(
                "product_packaging_id",
                "idx_purchase_items_product_packaging",
            );
        });

        DB::statement("
            ALTER TABLE supplier_purchase_items
            ADD CONSTRAINT chk_purchase_items_unit
            CHECK (char_length(btrim(purchase_unit)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_purchase_items_quantity
            CHECK (quantity BETWEEN 1 AND 999),
            ADD CONSTRAINT chk_purchase_items_conversion
            CHECK (conversion_factor BETWEEN 1 AND 9999999999),
            ADD CONSTRAINT chk_purchase_items_unit_price
            CHECK (unit_price BETWEEN 0 AND 999999999),
            ADD CONSTRAINT chk_purchase_items_subtotal
            CHECK (subtotal BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_purchase_items_item_source
            CHECK (
                (raw_material_id IS NOT NULL AND product_id IS NULL)
                OR
                (raw_material_id IS NULL AND product_id IS NOT NULL)
            ),
            ADD CONSTRAINT chk_purchase_items_packaging_source
            CHECK (
                (raw_material_packaging_id IS NOT NULL AND product_packaging_id IS NULL)
                OR
                (raw_material_packaging_id IS NULL AND product_packaging_id IS NOT NULL)
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("supplier_purchase_items");
    }
};
