<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->bigInteger('base_price')->default(0);
            $table->bigInteger('stock')->default(0);
            $table->string('stock_type', 20)->default('static');
            $table->string('image_url', 2048)->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->string('status', 20)->default('available');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->string('image_path', 500)->nullable();

            $table->index('category_id', 'idx_products_category');
            $table->index('status', 'idx_products_status');
        });

        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT chk_products_name
            CHECK (char_length(btrim(name)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_products_description_length
            CHECK (description IS NULL OR char_length(description) <= 1000),
            ADD CONSTRAINT chk_products_base_price
            CHECK (base_price BETWEEN 0 AND 999999999),
            ADD CONSTRAINT chk_products_stock
            CHECK (stock BETWEEN 0 AND 9999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
