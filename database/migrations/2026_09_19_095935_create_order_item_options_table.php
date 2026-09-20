<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('product_option_values')->cascadeOnDelete();
            $table->decimal('extra_price_snapshot', 12, 2)->default(0);

            $table->index('order_item_id', 'idx_order_item_options_item');
        });
    }
    public function down(): void { Schema::dropIfExists('order_item_options'); }
};
