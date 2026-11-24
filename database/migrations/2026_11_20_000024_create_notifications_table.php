<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 255);
            $table->string('notifiable_type', 255);
            $table->bigInteger('notifiable_id');
            $table->jsonb('data');
            $table->timestampTz('read_at')->nullable();
            $table->timestampTz('created_at')->nullable();
            $table->timestampTz('updated_at')->nullable();

            $table->index(
                ['notifiable_type', 'notifiable_id', 'read_at', 'created_at'],
                'notifications_recipient_read_created_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
