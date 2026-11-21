<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('supplier_purchases')->restrictOnDelete();
            $table->string('item_type', 20);
            $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials')->nullOnDelete();
            $table->foreignId('raw_material_packaging_id')->nullable()->constrained('raw_material_packagings')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_packaging_id')->nullable()->constrained('product_packagings')->nullOnDelete();
            $table->string('item_name_snapshot', 100);
            $table->string('packaging_name_snapshot', 100);
            $table->string('purchase_unit', 50);
            $table->bigInteger('quantity');
            $table->bigInteger('conversion_factor');
            $table->bigInteger('unit_price');
            $table->bigInteger('subtotal');

            $table->index('purchase_id', 'idx_purchase_items_purchase');
            $table->index('raw_material_id', 'idx_purchase_items_raw_material');
            $table->index('product_id', 'idx_purchase_items_product');
            $table->index('raw_material_packaging_id', 'idx_purchase_items_raw_packaging');
            $table->index('product_packaging_id', 'idx_purchase_items_product_packaging');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE supplier_purchase_items
            ADD CONSTRAINT chk_purchase_items_type CHECK (item_type IN ('raw_material', 'product')),
            ADD CONSTRAINT chk_purchase_items_item_source CHECK (
                (item_type = 'raw_material' AND product_id IS NULL AND product_packaging_id IS NULL)
                OR
                (item_type = 'product' AND raw_material_id IS NULL AND raw_material_packaging_id IS NULL)
            ),
            ADD CONSTRAINT chk_purchase_items_unit CHECK (char_length(btrim(purchase_unit)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_purchase_items_item_snapshot CHECK (char_length(btrim(item_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_purchase_items_packaging_snapshot CHECK (char_length(btrim(packaging_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_purchase_items_quantity CHECK (quantity BETWEEN 1 AND 999),
            ADD CONSTRAINT chk_purchase_items_conversion CHECK (conversion_factor BETWEEN 1 AND 9999999999),
            ADD CONSTRAINT chk_purchase_items_unit_price CHECK (unit_price BETWEEN 0 AND 999999999),
            ADD CONSTRAINT chk_purchase_items_subtotal CHECK (subtotal BETWEEN 0 AND 99999999999999)
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_purchase_items');
    }
};
