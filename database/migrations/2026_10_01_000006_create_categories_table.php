<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("categories", function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent();
        });

        DB::statement("
            ALTER TABLE categories
            ADD CONSTRAINT chk_categories_name
            CHECK (char_length(btrim(name)) BETWEEN 1 AND 50)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("categories");
    }
};
