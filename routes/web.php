<?php


use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('auth/check', [AuthController::class, 'checkAccount'])->name('auth.check');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['can:view-inventory'])->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::resource('categories', App\Http\Controllers\CategoryController::class);
        Route::get('stock/add', [InventoryController::class, 'stock'])->name('inventory.stock.add');
        Route::post('stock/store', [InventoryController::class, 'storeStock'])->name('inventory.stock.store');
        Route::get('stock/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::post('stock/transfer/store', [InventoryController::class, 'storeTransfer'])->name('inventory.transfer.store');
    });

    Route::middleware(['can:view-pos'])->group(function () {
        Route::get('pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('pos', [POSController::class, 'store'])->name('pos.store');
        Route::post('pos/check-stock', [POSController::class, 'checkStock'])->name('pos.checkStock');
        Route::post('pos/transfer-stock', [POSController::class, 'transferStock'])->name('pos.transferStock');
        Route::resource('customers', App\Http\Controllers\CustomerController::class);
    });

    Route::middleware(['can:view-profit'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
        Route::get('reports/items', [ReportController::class, 'saleItemsReport'])->name('reports.items');
    });

    Route::middleware(['can:admin'])->group(function () {
        Route::resource('accounts', AccountController::class);
    });
});


