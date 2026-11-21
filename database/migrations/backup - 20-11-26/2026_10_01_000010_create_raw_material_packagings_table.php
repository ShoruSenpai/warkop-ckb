<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("raw_material_packagings", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("raw_material_id")
                ->constrained("raw_materials")
                ->cascadeOnDelete();
            $table->string("purchase_unit", 20);
            $table->bigInteger("conversion_factor");
            $table->boolean("is_active")->default(true);
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent();

            $table->index(
                "raw_material_id",
                "idx_raw_material_packaging_material",
            );
        });

        DB::statement("
            ALTER TABLE raw_material_packagings
            ADD CONSTRAINT chk_raw_material_packagings_unit
            CHECK (char_length(btrim(purchase_unit)) BETWEEN 1 AND 20),
            ADD CONSTRAINT chk_raw_material_packagings_conversion
            CHECK (conversion_factor BETWEEN 1 AND 9999999)
        ");

        DB::statement("
            CREATE UNIQUE INDEX uq_raw_material_packaging_unit
            ON raw_material_packagings (raw_material_id, lower(btrim(purchase_unit)))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("raw_material_packagings");
    }
};
