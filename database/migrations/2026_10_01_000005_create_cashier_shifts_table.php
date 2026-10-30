<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('employees')->cascadeOnDelete();
            $table->timestampTz('start_time')->useCurrent();
            $table->timestampTz('end_time')->nullable();
            $table->bigInteger('starting_cash')->default(0);
            $table->bigInteger('ending_cash')->nullable();
            $table->bigInteger('total_sales_cash')->default(0);
            $table->bigInteger('total_sales_qris')->default(0);
            $table->bigInteger('total_sales_transfer')->default(0);
            $table->string('status', 20)->default('open');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('cashier_id', 'idx_cashier_shifts_cashier');
            $table->index('status', 'idx_cashier_shifts_status');
        });

        DB::statement("
            ALTER TABLE cashier_shifts
            ADD CONSTRAINT chk_cashier_shifts_starting_cash
            CHECK (starting_cash BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_cashier_shifts_ending_cash
            CHECK (ending_cash IS NULL OR ending_cash BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_cashier_shifts_total_sales_cash
            CHECK (total_sales_cash BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_cashier_shifts_total_sales_qris
            CHECK (total_sales_qris BETWEEN 0 AND 99999999999999),
            ADD CONSTRAINT chk_cashier_shifts_total_sales_transfer
            CHECK (total_sales_transfer BETWEEN 0 AND 99999999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
