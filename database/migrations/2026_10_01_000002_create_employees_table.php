<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_code', 20)->unique();
            $table->string('full_name', 100);
            $table->string('position', 50);
            $table->string('phone_number', 20)->nullable();
            $table->string('pin_code', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index('position', 'idx_employees_position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
