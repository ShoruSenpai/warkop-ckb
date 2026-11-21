<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("order_items", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("order_id")
                ->constrained("orders")
                ->cascadeOnDelete();
            $table
                ->foreignId("product_id")
                ->constrained("products")
                ->cascadeOnDelete();
            $table->integer("quantity")->default(1);
            $table->bigInteger("unit_price");
            $table->bigInteger("option_price")->default(0);
            $table->bigInteger("subtotal");

            $table->index("order_id", "idx_order_items_order");
            $table->index("product_id", "idx_order_items_product");
        });

        DB::statement("
            ALTER TABLE order_items
            ADD CONSTRAINT chk_order_items_quantity
            CHECK (quantity BETWEEN 1 AND 999),
            ADD CONSTRAINT chk_order_items_unit_price
            CHECK (unit_price BETWEEN 0 AND 999999999),
            ADD CONSTRAINT chk_order_items_option_price
            CHECK (option_price BETWEEN 0 AND 999999999),
            ADD CONSTRAINT chk_order_items_subtotal
            CHECK (subtotal BETWEEN 0 AND 99999999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("order_items");
    }
};
