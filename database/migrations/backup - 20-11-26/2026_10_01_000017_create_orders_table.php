<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50)->unique();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->cascadeOnDelete();
            $table->foreignId('cashier_id')->constrained('employees')->cascadeOnDelete();
            $table->bigInteger('gross_amount');
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('final_amount');
            $table->string('payment_method', 20);
            $table->string('payment_status', 20)->default('paid');
            $table->timestampTz('paid_at')->nullable()->useCurrent();
            $table->timestampTz('transaction_time')->useCurrent();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('cashier_shift_id', 'idx_orders_shift');
            $table->index('transaction_time', 'idx_orders_tx_time');
            $table->index('payment_method', 'idx_orders_method');
        });

        DB::statement("
            ALTER TABLE orders
            ADD CONSTRAINT chk_orders_invoice
            CHECK (char_length(btrim(invoice_code)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_orders_gross_amount
            CHECK (gross_amount BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_orders_discount_amount
            CHECK (discount_amount BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_orders_final_amount
            CHECK (final_amount BETWEEN 0 AND 99999999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
