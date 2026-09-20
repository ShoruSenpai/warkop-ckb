<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('unit_measurement', 20);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }
    public function down(): void { Schema::dropIfExists('raw_materials'); }
};
