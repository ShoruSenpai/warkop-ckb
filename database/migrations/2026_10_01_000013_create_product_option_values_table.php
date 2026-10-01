<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_group_id')->constrained('product_option_groups')->cascadeOnDelete();
            $table->string('option_name', 100);
            $table->bigInteger('extra_price')->default(0);

            $table->index('option_group_id', 'idx_option_values_group');
        });

        DB::statement("
            ALTER TABLE product_option_values
            ADD CONSTRAINT chk_option_values_name
            CHECK (char_length(btrim(option_name)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_option_values_extra_price
            CHECK (extra_price BETWEEN 0 AND 999999999)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
