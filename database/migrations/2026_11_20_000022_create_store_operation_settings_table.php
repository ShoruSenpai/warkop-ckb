<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_operation_settings', function (Blueprint $table) {
            $table->smallInteger('id')->default(1)->primary();
            $table->string('operation_mode', 20)->default('automatic');
            $table->boolean('manual_is_open')->default(true);
            $table->time('open_time')->default('08:00:00');
            $table->time('close_time')->default('22:00:00');
            $table->timestampTz('force_closed_until')->nullable();
            $table->string('force_close_reason', 255)->nullable();
            $table->foreignId('force_closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('created_at')->nullable();
            $table->timestampTz('updated_at')->nullable();
        });

        DB::statement(<<<'SQL'
            ALTER TABLE store_operation_settings
            ADD CONSTRAINT chk_store_operation_singleton CHECK (id = 1),
            ADD CONSTRAINT chk_store_operation_mode CHECK (operation_mode IN ('automatic', 'manual'))
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_operation_settings');
    }
};
