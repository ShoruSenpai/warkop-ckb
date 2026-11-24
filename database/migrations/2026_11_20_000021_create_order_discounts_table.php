<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->string('discount_name_snapshot', 100);
            $table->string('discount_type_snapshot', 20);
            $table->decimal('discount_value_snapshot', 12, 2);
            $table->bigInteger('discount_amount');
            $table->timestampTz('created_at')->useCurrent();

            $table->index('order_id', 'idx_order_discounts_order');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE order_discounts
            ADD CONSTRAINT chk_order_discounts_name CHECK (char_length(btrim(discount_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_order_discounts_value CHECK (discount_value_snapshot >= 0),
            ADD CONSTRAINT chk_order_discounts_amount CHECK (discount_amount BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_order_discounts_percentage CHECK (discount_type_snapshot <> 'percentage' OR discount_value_snapshot BETWEEN 0 AND 100)
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('order_discounts');
    }
};
