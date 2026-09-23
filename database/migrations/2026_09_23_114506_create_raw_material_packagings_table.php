<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_packagings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('raw_material_id')
                ->constrained('raw_materials')
                ->cascadeOnDelete();

            $table->string('purchase_unit', 20);
            $table->decimal('conversion_factor', 12, 3);

            $table->boolean('is_active')->default(true);

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(
                ['raw_material_id', 'purchase_unit'],
                'uq_raw_material_packaging_unit'
            );

            $table->index(
                'raw_material_id',
                'idx_raw_material_packaging_material'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_packagings');
    }
};
