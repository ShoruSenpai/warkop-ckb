<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserRoleSeeder extends Seeder
{
    /**
     * Create predictable development accounts and their employee profiles.
     * The seeder is idempotent and never resets an existing password.
     */
    public function run(): void
    {
        if (app()->environment("production")) {
            throw new RuntimeException(
                "UserRoleSeeder is for development/testing only.",
            );
        }

        $accounts = [
            [
                "key" => "developer",
                "username" => "ShoruSenpai",
                "email" => "shorusenpai@gmail.com",
                "role" => "owner",
                "employee_code" => "dev_001",
                "full_name" => "Shoru Senpai",
                "position" => "Developer",
                "phone_number" => "08123456789",
            ],
            [
                "key" => "owner",
                "username" => "owner cihuy",
                "email" => "owner@gmail.com",
                "role" => "owner",
                "employee_code" => "owner_001",
                "full_name" => "Owner Cihuy",
                "position" => "Owner",
                "phone_number" => "08123456789",
            ],
            [
                "key" => "admin",
                "username" => "Cici",
                "email" => "cici@gmail.com",
                "role" => "admin",
                "employee_code" => "adm_001",
                "full_name" => "Cici Admin",
                "position" => "Admin",
                "phone_number" => "08123456789",
            ],
            [
                "key" => "barista",
                "username" => "Pricil",
                "email" => "pricil@gmail.com",
                "role" => "employee",
                "employee_code" => "brs_001",
                "full_name" => "Pricil",
                "position" => "Barista",
                "phone_number" => "08123456789",
            ],
            [
                "key" => "cashier_main",
                "username" => "Cashier Account",
                "email" => "cashierckb01@gmail.com",
                "role" => "cashier",
                "employee_code" => "csh_002",
                "full_name" => "Kasir Akun Utama",
                "position" => "Cashier",
                "phone_number" => "08123456789",
            ],
            [
                "key" => "cashier_cece",
                "username" => "Cece Kasir",
                "email" => "caca01@gmail.com",
                "role" => "cashier",
                "employee_code" => "csh_001",
                "full_name" => "Cece Gemini",
                "position" => "Cashier",
                "phone_number" => "08123456789",
            ],
        ];

        DB::transaction(function () use ($accounts): void {
            foreach ($accounts as $account) {
                $userId = $this->upsertUser($account);
                $this->upsertEmployee($userId, $account);
            }
        });
    }

    private function upsertUser(array $account): int
    {
        $now = now();
        $user = DB::table("users")->where("email", $account["email"])->first();

        if ($user) {
            DB::table("users")
                ->where("id", $user->id)
                ->update([
                    "username" => $account["username"],
                    "role" => $account["role"],
                    "updated_at" => $now,
                ]);

            // Deliberately preserve an existing password on repeated seeding.
            return (int) $user->id;
        }

        $password =
            config("seeding.accounts.{$account["key"]}") ?:
            config("seeding.default_password");

        if (!is_string($password) || $password === "") {
            throw new RuntimeException(
                "Set SEED_DEFAULT_PASSWORD or the account-specific seed password before creating demo users.",
            );
        }

        return (int) DB::table("users")->insertGetId([
            "username" => $account["username"],
            "email" => $account["email"],
            "password" => Hash::make($password),
            "role" => $account["role"],
            "created_at" => $now,
            "updated_at" => $now,
        ]);
    }

    private function upsertEmployee(int $userId, array $account): void
    {
        $now = now();
        $employeeByUser = DB::table("employees")
            ->where("user_id", $userId)
            ->first();
        $employeeByCode = DB::table("employees")
            ->where("employee_code", $account["employee_code"])
            ->first();

        if (
            $employeeByCode &&
            (!$employeeByUser ||
                (int) $employeeByCode->id !== (int) $employeeByUser->id)
        ) {
            throw new RuntimeException(
                "Employee code {$account["employee_code"]} is already assigned to another employee.",
            );
        }

        $employeeData = [
            "user_id" => $userId,
            "employee_code" => $account["employee_code"],
            "full_name" => $account["full_name"],
            "position" => $account["position"],
            "phone_number" => $account["phone_number"],
            "is_active" => true,
            "updated_at" => $now,
        ];

        if ($employeeByUser) {
            DB::table("employees")
                ->where("id", $employeeByUser->id)
                ->update($employeeData);
            return;
        }

        if ($employeeByCode) {
            DB::table("employees")
                ->where("id", $employeeByCode->id)
                ->update($employeeData);
            return;
        }

        $employeeData["created_at"] = $now;
        DB::table("employees")->insert($employeeData);
    }
}
