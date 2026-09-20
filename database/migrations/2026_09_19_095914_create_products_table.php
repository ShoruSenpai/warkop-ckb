<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('stock_type', 20)->default('static');
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->string('status', 20)->default('available');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('category_id', 'idx_products_category');
            $table->index('status', 'idx_products_status');
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
