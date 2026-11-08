<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            "username" => "ShoruSenpai",
            "email" => "shorusenpai@gmail.com",
            "password" => Hash::make("@ShoruKun01"),
            "role" => "owner",
        ]);

        Employee::create([
            "user_id" => $owner->id,
            "employee_code" => "dev_001",
            "full_name" => "Shoru Senpai",
            "position" => "Developer",
            "phone_number" => "08123456789",
            "is_active" => 1,
        ]);

        $owner2 = User::create([
            "username" => "owner cihuy",
            "email" => "owner@gmail.com",
            "password" => Hash::make("admin123"),
            "role" => "owner",
        ]);

        Employee::create([
            "user_id" => $owner2->id,
            "employee_code" => "owner_001",
            "full_name" => "Shoru Senpai",
            "position" => "Owner",
            "phone_number" => "08123456789",
            "is_active" => 1,
        ]);

        $admin = User::create([
            "username" => "Cici",
            "email" => "cici@gmail.com",
            "password" => Hash::make("admin123"),
            "role" => "admin",
        ]);

        Employee::create([
            "user_id" => $admin->id,
            "employee_code" => "adm_001",
            "full_name" => "Cici Admin",
            "position" => "Admin",
            "phone_number" => "08123456789",
            "is_active" => 1,
        ]);

        $barista = User::create([
            "username" => "Pricil",
            "email" => "pricil@gmail.com",
            "password" => Hash::make("admin123"),
            "role" => "employee",
        ]);

        Employee::create([
            "user_id" => $barista->id,
            "employee_code" => "brs_001",
            "full_name" => "Pricil",
            "position" => "Barista",
            "phone_number" => "08123456789",
            "is_active" => 1,
        ]);

        User::create([
            "username" => "Cashier Account",
            "email" => "cashierckb01@gmail.com",
            "password" => Hash::make("admin123"),
            "role" => "cashier",
        ]);

        $cashierCece = User::create([
            "username" => "Cece Kasir",
            "email" => "caca01@gmail.com",
            "password" => Hash::make("admin123"),
            "role" => "cashier",
        ]);

        Employee::create([
            "user_id" => $cashierCece->id,
            "employee_code" => "csh_001",
            "full_name" => "Cece Gemini",
            "position" => "Cashier",
            "phone_number" => "08123456789",
            "is_active" => 1,
        ]);
    }
}
