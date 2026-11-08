<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseReportController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\SupplierPurchaseController;
use App\Http\Middleware\ExtendTokenActivity;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API routes
|--------------------------------------------------------------------------
*/

Route::post("/login", [AuthController::class, "login"]);

/*
|--------------------------------------------------------------------------
| Authenticated API routes
|--------------------------------------------------------------------------
*/

Route::middleware(["auth:sanctum", ExtendTokenActivity::class])->group(
    function () {
        // Logout is available to every authenticated account.
        Route::post("/logout", [AuthController::class, "logout"]);

        /*
         * Add or remove roles in this middleware declaration to control
         * access to the current admin API surface in one place.
         */
        Route::middleware("api-role:owner,admin")->group(function () {
            // Categories: only owner may create; owner/admin may read.
            Route::get("/categories", [CategoryController::class, "index"]);
            Route::middleware("api-role:owner")->group(function () {
                Route::post("/categories", [CategoryController::class, "store"]);
            });

            // Products and product purchase packaging.
            Route::get("/products", [ProductController::class, "index"]);
            Route::post("/products", [ProductController::class, "store"]);
            Route::get("/products/{product}", [ProductController::class, "show"]);
            Route::put("/products/{product}", [ProductController::class, "update"]);
            Route::delete("/products/{product}", [ProductController::class, "destroy"]);
            Route::get("/products/{product}/packagings", [ProductController::class, "packagingIndex"]);
            Route::post("/products/{product}/packagings", [ProductController::class, "packagingStore"]);
            Route::put("/products/{product}/packagings/{packaging}", [ProductController::class, "packagingUpdate"]);
            Route::delete("/products/{product}/packagings/{packaging}", [ProductController::class, "packagingDestroy"]);

            // Raw materials and their purchase packaging.
            Route::get("/raw-materials", [RawMaterialController::class, "index"]);
            Route::post("/raw-materials", [RawMaterialController::class, "store"]);
            Route::put("/raw-materials/{rawMaterial}", [RawMaterialController::class, "update"]);
            Route::delete("/raw-materials/{rawMaterial}", [RawMaterialController::class, "destroy"]);
            Route::get("/raw-materials/{rawMaterial}/packagings", [RawMaterialController::class, "packagingIndex"]);
            Route::post("/raw-materials/{rawMaterial}/packagings", [RawMaterialController::class, "packagingStore"]);
            Route::put("/raw-materials/{rawMaterial}/packagings/{packaging}", [RawMaterialController::class, "packagingUpdate"]);
            Route::delete("/raw-materials/{rawMaterial}/packagings/{packaging}", [RawMaterialController::class, "packagingDestroy"]);

            // Supplier purchases and purchase reports.
            Route::get("/inventory-items", [SupplierPurchaseController::class, "getInventoryItems"]);
            Route::get("/supplier-suggestions", [SupplierPurchaseController::class, "getSupplierSuggestions"]);
            Route::post("/supplier-purchases", [SupplierPurchaseController::class, "store"]);
            Route::get("/reports/purchases", [PurchaseReportController::class, "index"]);
        });
    },
);
