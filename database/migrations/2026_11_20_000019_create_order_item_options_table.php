<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->restrictOnDelete();
            $table->foreignId('option_value_id')->nullable()->constrained('product_option_values')->nullOnDelete();
            $table->string('option_group_name_snapshot', 100);
            $table->string('option_name_snapshot', 100);
            $table->bigInteger('extra_price_snapshot')->default(0);

            $table->index('order_item_id', 'idx_order_item_options_item');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE order_item_options
            ADD CONSTRAINT chk_order_item_options_group_snapshot CHECK (char_length(btrim(option_group_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_order_item_options_name_snapshot CHECK (char_length(btrim(option_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_order_item_options_extra_price CHECK (extra_price_snapshot BETWEEN 0 AND 999999999)
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
