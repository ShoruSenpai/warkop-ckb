<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("raw_materials", function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->string("unit_measurement", 20);
            $table->bigInteger("current_stock")->default(0);
            $table->bigInteger("minimum_stock")->default(0);
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent();
        });

        DB::statement("
            ALTER TABLE raw_materials
            ADD CONSTRAINT chk_raw_materials_name
            CHECK (char_length(btrim(name)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_raw_materials_current_stock
            CHECK (current_stock BETWEEN 0 AND 9999999999),
            ADD CONSTRAINT chk_raw_materials_minimum_stock
            CHECK (minimum_stock BETWEEN 0 AND 999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("raw_materials");
    }
};
