<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// custom controller
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\SupplierPurchaseController;
use App\Http\Controllers\Api\AuthController;

// custom middleware
use App\Http\Middleware\ExtendTokenActivity;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', ExtendTokenActivity::class])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // master product & raw materials
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/raw-materials', [RawMaterialController::class, 'store']);
    Route::get('/raw-materials', [RawMaterialController::class, 'index']);

    // supplier purchases
    Route::get('/inventory-items', [SupplierPurchaseController::class, 'getInventoryItems']);
    Route::get('/supplier-suggestions', [SupplierPurchaseController::class, 'getSupplierSuggestions']);
    Route::post('/supplier-purchases', [SupplierPurchaseController::class, 'store']);
});

