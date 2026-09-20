<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('shift_name', 50);
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['employee_id', 'schedule_date'], 'idx_emp_schedules_date');
        });
    }
    public function down(): void { Schema::dropIfExists('employee_schedules'); }
};
