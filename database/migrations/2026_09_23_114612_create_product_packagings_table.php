<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_packagings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('purchase_unit', 20);
            $table->decimal('conversion_factor', 12, 3);

            $table->boolean('is_active')->default(true);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(
                ['product_id', 'purchase_unit'],
                'uq_product_packaging_unit'
            );

            $table->index(
                'product_id',
                'idx_product_packaging_product'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_packagings');
    }
};
