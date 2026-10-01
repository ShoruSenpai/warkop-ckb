<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('group_name', 100);
            $table->boolean('is_required')->default(false);
            $table->integer('max_choices')->default(1);

            $table->index('product_id', 'idx_option_groups_product');
        });

        DB::statement("
            ALTER TABLE product_option_groups
            ADD CONSTRAINT chk_option_groups_name
            CHECK (char_length(btrim(group_name)) BETWEEN 1 AND 100),
            ADD CONSTRAINT chk_option_groups_max_choices
            CHECK (max_choices BETWEEN 1 AND 100)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_groups');
    }
};
