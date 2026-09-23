<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_purchase_items', function (Blueprint $table) {
            $table->foreignId('raw_material_packaging_id')
                ->nullable()
                ->after('raw_material_id')
                ->constrained('raw_material_packagings')
                ->nullOnDelete();

            $table->foreignId('product_packaging_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_packagings')
                ->nullOnDelete();

            $table->index(
                'raw_material_packaging_id',
                'idx_purchase_items_raw_packaging'
            );

            $table->index(
                'product_packaging_id',
                'idx_purchase_items_product_packaging'
            );
        });
    }

    public function down(): void
    {
        Schema::table('supplier_purchase_items', function (Blueprint $table) {
            $table->dropForeign(['raw_material_packaging_id']);
            $table->dropForeign(['product_packaging_id']);

            $table->dropIndex('idx_purchase_items_raw_packaging');
            $table->dropIndex('idx_purchase_items_product_packaging');

            $table->dropColumn([
                'raw_material_packaging_id',
                'product_packaging_id',
            ]);
        });
    }
};
