<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("username", 20)->unique();
            $table->string("email", 50)->unique();
            $table->string("password", 255);
            $table->string("role", 20)->default("employee");
            $table->rememberToken();
            $table->timestampTz("created_at")->useCurrent();
            $table->timestampTz("updated_at")->useCurrent();
        });

        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_role
            CHECK (role IN ('owner', 'admin', 'cashier', 'employee'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
