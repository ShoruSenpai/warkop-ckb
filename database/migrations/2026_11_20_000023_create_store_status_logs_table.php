<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_status_logs', function (Blueprint $table) {
            $table->id();
            $table->boolean('previous_is_open')->nullable();
            $table->boolean('new_is_open');
            $table->string('event_type', 30);
            $table->string('reason', 255)->nullable();
            $table->timestampTz('effective_until')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('changed_at');
            $table->timestampTz('created_at')->useCurrent();

            $table->index('changed_at', 'idx_store_status_changed');
            $table->index(['event_type', 'changed_at'], 'idx_store_status_event');
            $table->index(['changed_by_user_id', 'changed_at'], 'idx_store_status_actor_changed');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE store_status_logs
            ADD CONSTRAINT chk_store_status_event_type CHECK (
                event_type IN (
                    'automatic_open',
                    'automatic_close',
                    'manual_open',
                    'manual_close',
                    'force_close',
                    'force_close_expired'
                )
            )
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_status_logs');
    }
};
