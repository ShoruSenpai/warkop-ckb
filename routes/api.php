<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// custom controller
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\SupplierPurchaseController;

// custom middleware
use App\Http\Middleware\ExtendTokenActivity;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', ExtendTokenActivity::class])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // master category
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);

    // master product
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // product packaging
    Route::get(
        '/products/{product}/packagings',
        [ProductController::class, 'packagingIndex']
    );

    Route::post(
        '/products/{product}/packagings',
        [ProductController::class, 'packagingStore']
    );

    Route::put(
        '/products/{product}/packagings/{packaging}',
        [ProductController::class, 'packagingUpdate']
    );

    Route::delete(
        '/products/{product}/packagings/{packaging}',
        [ProductController::class, 'packagingDestroy']
    );

    // master raw material

    Route::get('/raw-materials', [RawMaterialController::class, 'index']);
    Route::post('/raw-materials', [RawMaterialController::class, 'store']);
    Route::put('/raw-materials/{rawMaterial}', [RawMaterialController::class, 'update']);
    Route::delete('/raw-materials/{rawMaterial}', [RawMaterialController::class, 'destroy']);

    // raw material packaging
    Route::get(
        '/raw-materials/{rawMaterial}/packagings',
        [RawMaterialController::class, 'packagingIndex']
    );

    Route::post(
        '/raw-materials/{rawMaterial}/packagings',
        [RawMaterialController::class, 'packagingStore']
    );

    Route::put(
        '/raw-materials/{rawMaterial}/packagings/{packaging}',
        [RawMaterialController::class, 'packagingUpdate']
    );

    Route::delete(
        '/raw-materials/{rawMaterial}/packagings/{packaging}',
        [RawMaterialController::class, 'packagingDestroy']
    );

    // supplier purchases
    Route::get('/inventory-items', [SupplierPurchaseController::class, 'getInventoryItems']);
    Route::get('/supplier-suggestions', [SupplierPurchaseController::class, 'getSupplierSuggestions']);
    Route::post('/supplier-purchases', [SupplierPurchaseController::class, 'store']);
});

