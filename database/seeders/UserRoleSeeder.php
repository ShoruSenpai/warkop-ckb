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
        $admin = User::FirstOrCreate([
            'username' => 'Cici',
            'email' => 'cici@gmail.com',
            'password'=> Hash::make('admin123'),
            'role' => 'admin'
        ]);

        Employee::create([
            'user_id' => $admin->id,
            'employee_code' => 'adm_001',
            'full_name' => 'Cici Admin',
            'position' => 'Admin',
            'phone_number' => '08123456789',
            'is_active' => 1
        ]);

//        $owner = User::create([
//            'username' => 'ShoruSenpai',
//            'email' => 'shorusenpai@gmail.com',
//            'password'=> Hash::make('@ShoruKun01'),
//            'role' => 'owner'
//        ]);
//
//        Employee::create([
//            'user_id' => $owner->id,
//            'employee_code' => 'dev_001',
//            'full_name' => 'Shoru Senpai',
//            'position' => 'Developer',
//            'phone_number' => '08123456789',
//            'is_active' => 1
//        ]);
//
//        $barista = User::create([
//            'username' => 'Pricil',
//            'email' => 'pricil@gmail.com',
//            'password'=> Hash::make('admin123'),
//            'role' => 'employee'
//        ]);
//
//        Employee::create([
//            'user_id' => $barista->id,
//            'employee_code' => 'brs_001',
//            'full_name' => 'Pricil',
//            'position' => 'Barista',
//            'phone_number' => '08123456789',
//            'is_active' => 1
//        ]);
//
//        $cashier = User::create([
//            'username' => 'Cashier Account',
//            'email' => 'cashierckb01@gmail.com',
//            'password'=> Hash::make('admin123'),
//            'role' => 'cashier'
//        ]);
//
//        $cashierCaca = User::create([
//            'username' => 'Caca Kasir',
//            'email' => 'caca01@gmail.com',
//            'password'=> Hash::make('admin123'),
//            'role' => 'cashier'
//        ]);
//
//        Employee::create([
//            'user_id' => $cashierCaca->id,
//            'employee_code' => 'csh_001',
//            'full_name' => 'Caca Uye',
//            'position' => 'Cashier',
//            'phone_number' => '08123456789',
//            'is_active' => 1
//        ]);

    }
}
