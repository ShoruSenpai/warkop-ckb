<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            "Coffee",
            "Non Coffee",
            "Base Milk",
            "Food",
            "Snack",
            "Cigarette",
        ];

        foreach ($categories as $name) {
            $now = now();
            $existing = DB::table("categories")->where("name", $name)->first();

            if ($existing) {
                DB::table("categories")
                    ->where("id", $existing->id)
                    ->update([
                        "updated_at" => $now,
                    ]);
                continue;
            }

            DB::table("categories")->insert([
                "name" => $name,
                "created_at" => $now,
                "updated_at" => $now,
            ]);
        }
    }
}
