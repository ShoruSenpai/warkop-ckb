<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_code', 20)->unique();
            $table->string('full_name', 100);
            $table->string('position', 50); // Cashier, Barista, Kitchen, Dishwasher
            $table->string('phone_number', 15)->nullable();
            $table->string('pin_code', 60)->nullable(); // 60 for bcrypt hash
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('employee_code', 'idx_employees_code');
            $table->index('position', 'idx_employees_position');
        });
    }
    public function down(): void { Schema::dropIfExists('employees'); }
};
