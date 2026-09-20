<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('supplier_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->string('supplier_name', 100);
            $table->decimal('total_cost', 12, 2);
            $table->date('purchase_date');
            $table->string('status', 20)->default('pending');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('supplier_purchases'); }
};
