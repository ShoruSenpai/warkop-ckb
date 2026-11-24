<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

require dirname(__DIR__, 2) . "/vendor/autoload.php";

$app = require dirname(__DIR__, 2) . "/bootstrap/app.php";
$app->make(Kernel::class)->bootstrap();

$role = $argv[1] ?? "admin";
if (!in_array($role, ["owner", "admin"], true)) {
    fwrite(STDERR, "Expected owner or admin role.\n");
    exit(2);
}

$user = new User(["username" => "Schedule Browser Check", "role" => $role]);
$user->setRelation(
    "employee",
    new Employee(["full_name" => "Schedule Browser Check"]),
);
Auth::login($user, false);

echo view("schedule.index")->render();
