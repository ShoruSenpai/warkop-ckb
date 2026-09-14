<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50)->unique();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->cascadeOnDelete();
            $table->foreignId('cashier_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2);
            $table->string('payment_method', 20); // cash, qris, transfer
            $table->string('payment_status', 20)->default('paid'); // paid, cancelled
            $table->timestampTz('paid_at')->nullable()->useCurrent();
            $table->timestampTz('transaction_time')->useCurrent();
            $table->timestampsTz();

            $table->index('invoice_code', 'idx_orders_invoice');
            $table->index('cashier_shift_id', 'idx_orders_shift');
            $table->index('transaction_time', 'idx_orders_tx_time');
            $table->index('payment_method', 'idx_orders_method');
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
