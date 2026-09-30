<?php

use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name(
        'login.process',
    );
});

Route::get('/', function () {
    if (Auth::check()) {
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
    })->name('products.index');
    Route::get('/products/create', function () {
        return view('products.create');
    })->name('products.create');
    Route::get('/products/{product}/edit', function ($product) {
        return view('products.edit', ['productId' => $product]);
    })->name('products.edit');

    Route::get('/raw-materials', function () {
        return view('raw-materials.index');
    })->name('raw-material.index');

    Route::get('/suppliers', function () {
        return view('suppliers.index');
    })->name('supplier.index');

    Route::get('/reports', function () {
        return view('reports.sales');
    })->name('report.index');

    Route::get('/reports/transactions', function () {
        return view('reports.transactions');
    })->name('reports.transactions');

    Route::get('/reports/sales', function () {
        return view('reports.sales');
    })->name('reports.sales');

    Route::get('/reports/purchases', function () {
        return view('reports.purchases');
    })->name('reports.purchases');

    Route::get('/reports/stock', function () {
        return view('reports.stock');
    })->name('reports.stock');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings');

    Route::get('/users', function () {
        return view('settings.index');
    })->name('users.index');

    // middleware owner
    Route::middleware(['role:owner'])->group(function () {
        //
    });
});
