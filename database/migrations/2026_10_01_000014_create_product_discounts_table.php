<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->timestampTz('start_date');
            $table->timestampTz('end_date');
            $table->boolean('is_active')->default(true);

            $table->index('product_id', 'idx_discounts_product');
            $table->index(['start_date', 'end_date'], 'idx_discounts_period');
        });

        DB::statement("
            ALTER TABLE product_discounts
            ADD CONSTRAINT chk_product_discounts_value
            CHECK (discount_value >= 0),
            ADD CONSTRAINT chk_product_discounts_dates
            CHECK (end_date >= start_date),
            ADD CONSTRAINT chk_product_discounts_percentage
            CHECK (discount_type <> 'percentage' OR discount_value BETWEEN 0 AND 100)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_discounts');
    }
};
