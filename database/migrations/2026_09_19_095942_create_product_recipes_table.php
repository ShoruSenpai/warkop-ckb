<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('amount_needed', 10, 2);

            $table->index(['product_id', 'raw_material_id'], 'idx_recipes_product_material');
        });
    }
    public function down(): void { Schema::dropIfExists('product_recipes'); }
};
