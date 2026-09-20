<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\ExtendTokenActivity;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum', ExtendTokenActivity::class)->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/products', [ProductController::class, 'store']);

    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'You are logged in',
            'data' => $request->user()->load('employee')
        ]);
    });

    Route::get('/test-auth', function (Request $request) {
        return response()->json([
            'success' => true,
            'user_id' => $request->user()->id,
            'token_id' => $request->user()->currentAccessToken(),
        ]);
    });
});

