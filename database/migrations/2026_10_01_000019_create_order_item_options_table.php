<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('product_option_values')->cascadeOnDelete();
            $table->bigInteger('extra_price_snapshot')->default(0);

            $table->index('order_item_id', 'idx_order_item_options_item');
        });

        DB::statement("
            ALTER TABLE order_item_options
            ADD CONSTRAINT chk_order_item_options_extra_price
            CHECK (extra_price_snapshot BETWEEN 0 AND 999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
