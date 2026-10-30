<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("product_packagings", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("product_id")
                ->constrained("products")
                ->cascadeOnDelete();
            $table->string("purchase_unit", 20);
            $table->bigInteger("conversion_factor");
            $table->boolean("is_active")->default(true);
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent();

            $table->index("product_id", "idx_product_packaging_product");
        });

        DB::statement("
            ALTER TABLE product_packagings
            ADD CONSTRAINT chk_product_packagings_unit
            CHECK (char_length(btrim(purchase_unit)) BETWEEN 1 AND 20),
            ADD CONSTRAINT chk_product_packagings_conversion
            CHECK (conversion_factor BETWEEN 1 AND 9999999)
        ");

        DB::statement("
            CREATE UNIQUE INDEX uq_product_packaging_unit
            ON product_packagings (product_id, lower(btrim(purchase_unit)))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("product_packagings");
    }
};
