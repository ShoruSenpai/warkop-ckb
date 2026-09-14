<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('replacement_for_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('attendance_type', 20)->default('regular'); // regular, replacement, overtime
            $table->date('attendance_date');
            $table->timestampTz('clock_in');
            $table->timestampTz('clock_out')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('selfie_image_url', 255);
            $table->string('status', 20)->default('present'); // present, late, excused, absent, invalid
            $table->boolean('is_verified_by_manager')->default(false);
            $table->text('notes')->nullable();
            $table->timestampsTz();

            $table->index(['employee_id', 'attendance_date'], 'idx_attendances_emp_date');
            $table->index('status', 'idx_attendances_status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendances');
    }
};
