<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('employees')->cascadeOnDelete();
            $table->timestampTz('start_time')->useCurrent();
            $table->timestampTz('end_time')->nullable();
            $table->decimal('starting_cash', 12, 2)->default(0);
            $table->decimal('ending_cash', 12, 2)->nullable();
            $table->decimal('total_sales_cash', 12, 2)->default(0);
            $table->decimal('total_sales_qris', 12, 2)->default(0);
            $table->decimal('total_sales_transfer', 12, 2)->default(0);
            $table->string('status', 20)->default('open'); // open, closed
            $table->timestampsTz();

            $table->index('cashier_id', 'idx_cashier_shifts_cashier');
            $table->index('status', 'idx_cashier_shifts_status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cashier_shifts');
    }
};
