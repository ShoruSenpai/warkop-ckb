<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50)->unique('uq_orders_invoice_code');
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->restrictOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('cashier_name_snapshot', 100);
            $table->string('store_name_snapshot', 120);
            $table->string('status', 20);
            $table->string('order_type', 20);
            $table->bigInteger('gross_amount');
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('final_amount');
            $table->string('payment_method', 20);
            $table->string('payment_status', 20)->default('paid');
            $table->timestampTz('transaction_time')->useCurrent();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('receipt_issued_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('transaction_time', 'idx_orders_tx_time');
            $table->index('payment_method', 'idx_orders_method');
            $table->index('cashier_shift_id', 'idx_orders_shift');
            $table->index('cashier_id', 'idx_orders_cashier');
            $table->index(['status', 'payment_status'], 'idx_orders_status_payment');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE orders
            ADD CONSTRAINT chk_orders_invoice CHECK (char_length(btrim(invoice_code)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_orders_cashier_snapshot CHECK (char_length(btrim(cashier_name_snapshot)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_orders_store_snapshot CHECK (char_length(btrim(store_name_snapshot)) BETWEEN 1 AND 120),
            ADD CONSTRAINT chk_orders_gross_amount CHECK (gross_amount BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_orders_discount_amount CHECK (discount_amount BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_orders_final_amount CHECK (final_amount BETWEEN 0 AND 99999999999999)
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
