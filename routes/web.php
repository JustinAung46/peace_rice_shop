<?php


use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;

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
        Route::get('stock/transform', [InventoryController::class, 'transform'])->name('inventory.transform');
        Route::post('stock/transform/process', [InventoryController::class, 'processTransform'])->name('inventory.transform.process');
        Route::get('stock/batches', [InventoryController::class, 'batches'])->name('inventory.batches');
        Route::get('stock/movements', [StockMovementController::class, 'index'])->name('inventory.movements');
        Route::resource('warehouses', WarehouseController::class);
    });

    Route::middleware(['can:view-pos'])->group(function () {
        Route::get('pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('pos', [POSController::class, 'store'])->name('pos.store');
        Route::post('pos/check-stock', [POSController::class, 'checkStock'])->name('pos.checkStock');
        Route::post('pos/transfer-stock', [POSController::class, 'transferStock'])->name('pos.transferStock');
        Route::post('pos/sales/{sale}/cancel', [POSController::class, 'cancel'])->name('pos.sales.cancel');
        Route::resource('customers', App\Http\Controllers\CustomerController::class);
        Route::get('credits', [CreditController::class, 'index'])->name('credits.index');
        Route::get('credits/{customer}/history', [CreditController::class, 'history'])->name('credits.history');
        Route::post('credits/payment', [CreditController::class, 'storePayment'])->name('credits.payment.store');
    });

    Route::middleware(['can:view-profit'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
        Route::get('reports/items', [ReportController::class, 'saleItemsReport'])->name('reports.items');
        Route::get('reports/items/export', [ReportController::class, 'exportSaleItems'])->name('reports.items.export');
        Route::get('reports/receipts', [ReportController::class, 'receipts'])->name('reports.receipts');
    });

    Route::middleware(['can:admin'])->group(function () {
        Route::resource('accounts', AccountController::class);

        // Credit Payment: Edit, Delete, Audit Log
        Route::put('credits/payment/{payment}',    [\App\Http\Controllers\CreditController::class, 'updatePayment'])->name('credits.payment.update');
        Route::delete('credits/payment/{payment}', [\App\Http\Controllers\CreditController::class, 'destroyPayment'])->name('credits.payment.destroy');
        Route::get('credits/{customer}/audit',     [\App\Http\Controllers\CreditController::class, 'auditLog'])->name('credits.audit');

        // Suppliers
        Route::resource('suppliers', App\Http\Controllers\SupplierController::class);

        // Purchase Orders
        Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)
            ->except([]);
        Route::get('purchase-orders/{purchaseOrder}/receive',
            [App\Http\Controllers\PurchaseOrderController::class, 'createReceipt'])
            ->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchaseOrder}/receive',
            [App\Http\Controllers\PurchaseOrderController::class, 'storeReceipt'])
            ->name('purchase-orders.receive.store');
        Route::post('purchase-orders/{purchaseOrder}/payment',
            [App\Http\Controllers\PurchaseOrderController::class, 'storePayment'])
            ->name('purchase-orders.payment.store');
    });
});


