<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 20)->unique('uq_users_username');
            $table->string('email', 50)->unique('uq_users_email');
            $table->string('password', 255);
            $table->string('role', 20)->default('employee');
            $table->string('remember_token', 100)->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
        });

        DB::statement(<<<'SQL'
            ALTER TABLE users
            ADD CONSTRAINT chk_users_role
            CHECK (role IN ('owner', 'admin', 'cashier', 'employee'))
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
