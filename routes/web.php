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
    Route::post("/login", [AuthController::class, "processLogin"])->name(
        "login.process",
    );
});

Route::get("/", function () {
    return Auth::check()
        ? redirect()->route("dashboard.index")
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

    Route::get("/dashboard", fn() => view("dashboard.index"))->name(
        "dashboard.index",
    );

    // Product pages.
    Route::get("/products", fn() => view("products.index"))->name(
        "products.index",
    );
    Route::get("/products/create", fn() => view("products.create"))->name(
        "products.create",
    );
    Route::get(
        "/products/{product}/edit",
        fn($product) => view("products.edit", ["productId" => $product]),
    )->name("products.edit");

    // Inventory and supplier pages.
    Route::get("/raw-materials", fn() => view("raw-materials.index"))->name(
        "raw-material.index",
    );
    Route::get("/suppliers", fn() => view("suppliers.index"))->name(
        "supplier.index",
    );

    // Reports.
    Route::get("/reports/transactions", fn() => redirect()->route("reports.sales"))->name("reports.transactions");
    Route::get("/reports/sales", fn() => view("reports.sales.index"))->name("reports.sales",);
    Route::get("/reports/purchases", fn() => view("reports.purchases.index"),)->name("reports.purchases.index");
    Route::get("/reports/purchases/invoices", fn() => view("reports.purchases.invoices"),)->name("reports.purchases.invoices");
    Route::get("/reports/purchases/invoices/{purchase}", fn($purchase) => view("reports.purchases.show", ["purchaseId" => $purchase]))->whereNumber("purchase")->name("reports.purchases.show");


    Route::get("/users", fn() => view("users.index"))->name("users.index");
    Route::get("/notification", fn() => view("notification.index"))->name("notification.index",);
    Route::get("/settings", fn() => view("settings.index"))->name("settings.index");

    Route::get('/testing-503', fn() => view("testing.503"))->name("testing.index");
    // Add owner-only pages in a nested role:owner group when needed.
});
