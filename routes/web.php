<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Web\AuthController;
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
});

Route::get('/', function () {
    if(Auth::check()){
        return view('dashboard');
    }

    return view('auth.login');
});

    // group middleware (owner & admin)

Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/products', function () {
        return view('products.index');
    })->name('product.index');
    Route::get('/raw-materials', function () {
        return view('raw-materials.index');
    })->name('raw-material.index');
    Route::get('/suppliers', function () {
        return view('suppliers.index');
    })->name('supplier.index');
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('report.index');
    Route::get('/settings', function ()
    { return view('settings.index');
    })->name('settings');

    // middleware owner
    Route::middleware(['role:owner'])->group(function () {
        //
    });
});
