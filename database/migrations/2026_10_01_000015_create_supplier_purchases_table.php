<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->string('supplier_name', 100);
            $table->bigInteger('total_cost');
            $table->date('purchase_date');
            $table->string('status', 20)->default('pending');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
        });

        DB::statement("
            ALTER TABLE supplier_purchases
            ADD CONSTRAINT chk_supplier_purchases_invoice
            CHECK (char_length(btrim(invoice_number)) BETWEEN 1 AND 50),
            ADD CONSTRAINT chk_supplier_purchases_supplier
            CHECK (char_length(btrim(supplier_name)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_supplier_purchases_total
            CHECK (total_cost BETWEEN 0 AND 99999999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_purchases');
    }
};
