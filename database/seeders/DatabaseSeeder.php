<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            CategorySeeder::class,
            RawMaterialSeeder::class,
            SupplierPurchaseSeeder::class,
            ProductSeeder::class,
            StoreOperationSettingsSeeder::class,
            OrderHistorySeeder::class,
        ]);
    }
}
