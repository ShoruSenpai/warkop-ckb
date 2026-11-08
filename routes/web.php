<?php

use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/

Route::middleware("guest")->group(function () {
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "processLogin"])->name("login.process");
});

Route::get("/", function () {
    return Auth::check()
        ? redirect()->route("dashboard")
        : redirect()->route("login");
})->name("home");

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
|
| Keep the permitted admin roles in this middleware declaration. To adjust
| access later, update this role list or add a nested role group below.
*/

Route::middleware(["auth", "role:owner,admin"])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");

    Route::get("/dashboard", fn() => view("dashboard.index"))->name("dashboard");

    // Product pages.
    Route::get("/products", fn() => view("products.index"))->name("products.index");
    Route::get("/products/create", fn() => view("products.create"))->name("products.create");
    Route::get("/products/{product}/edit", fn($product) => view("products.edit", ["productId" => $product]))->name("products.edit");

    // Inventory and supplier pages.
    Route::get("/raw-materials", fn() => view("raw-materials.index"))->name("raw-material.index");
    Route::get("/suppliers", fn() => view("suppliers.index"))->name("supplier.index");

    // Reports.
    Route::get("/reports/transactions", fn() => view("reports.transactions.index"))->name("reports.transactions");
    Route::get("/reports/sales", fn() => view("reports.sales.index"))->name("reports.sales");
    Route::get("/reports/purchases", fn() => view("reports.purchases.index"))->name("reports.purchases.index");
    Route::get("/reports/stock", fn() => view("reports.stock.index"))->name("reports.stock");

    Route::get("/settings", fn() => view("settings.index"))->name("settings");

    // Add owner-only pages in a nested role:owner group when needed.
});
