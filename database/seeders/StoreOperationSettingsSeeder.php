<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreOperationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $settings = [
            'operation_mode' => 'automatic',
            'manual_is_open' => true,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'force_closed_until' => null,
            'force_close_reason' => null,
            'force_closed_by_user_id' => null,
            'updated_by_user_id' => null,
            'updated_at' => $now,
        ];

        $exists = DB::table('store_operation_settings')->where('id', 1)->exists();

        if ($exists) {
            // Preserve all existing operational settings on repeated seeding.
            return;
        }

        DB::table('store_operation_settings')->insert($settings + [
            'id' => 1,
            'created_at' => $now,
        ]);
    }
}
