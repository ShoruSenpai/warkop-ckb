<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('group_name', 50); // Spicy Level, Extra Toppings, Size, Ice/Hot
            $table->boolean('is_required')->default(false);
            $table->integer('max_choices')->default(1);

            $table->index('product_id', 'idx_option_groups_product');
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_option_groups');
    }
};
