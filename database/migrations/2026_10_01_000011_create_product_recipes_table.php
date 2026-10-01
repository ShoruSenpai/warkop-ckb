<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->bigInteger('amount_needed');

            $table->unique(['product_id', 'raw_material_id'], 'uq_recipes_product_material');
        });

        DB::statement("
            ALTER TABLE product_recipes
            ADD CONSTRAINT chk_product_recipes_amount
            CHECK (amount_needed BETWEEN 1 AND 9999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
