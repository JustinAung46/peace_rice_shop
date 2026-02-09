<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

use App\Http\Controllers\InventoryController;
Route::resource('inventory', InventoryController::class);
Route::get('stock/add', [InventoryController::class, 'stock'])->name('inventory.stock.add');
Route::post('stock/store', [InventoryController::class, 'storeStock'])->name('inventory.stock.store');
Route::get('stock/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
Route::post('stock/transfer/store', [InventoryController::class, 'storeTransfer'])->name('inventory.transfer.store');

use App\Http\Controllers\POSController;
Route::get('pos', [POSController::class, 'index'])->name('pos.index');
Route::post('pos', [POSController::class, 'store'])->name('pos.store');

use App\Http\Controllers\ReportController;
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
