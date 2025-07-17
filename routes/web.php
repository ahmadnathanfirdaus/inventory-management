<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\GoodsReceivedController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (will redirect to login if not authenticated)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Orders management
    Route::resource('orders', OrderController::class);

    // Goods received management
    Route::resource('goods-received', GoodsReceivedController::class)->except(['edit', 'update']);
    Route::get('goods-received/debug', function() {
        return view('goods-received.debug');
    })->name('goods-received.debug');
    Route::post('api/goods-received/get-order-details', [GoodsReceivedController::class, 'getOrderDetails'])
        ->name('goods-received.get-order-details');
    Route::get('api/goods-received/po-details', [GoodsReceivedController::class, 'getPODetails'])
        ->name('goods-received.po-details');
    Route::get('api/available-orders', [GoodsReceivedController::class, 'getAvailableOrders'])
        ->name('goods-received.available-orders');

    // Brand management
    Route::resource('brands', BrandController::class);
    Route::post('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])
        ->name('brands.toggle-status');
    Route::get('api/brands/search', [BrandController::class, 'search'])->name('brands.search');
    Route::get('api/brands/select', [BrandController::class, 'select'])->name('brands.select');

    // Distributor management
    Route::resource('distributors', DistributorController::class);
    Route::post('distributors/{distributor}/toggle-status', [DistributorController::class, 'toggleStatus'])
        ->name('distributors.toggle-status');
    Route::get('api/distributors/search', [DistributorController::class, 'search'])->name('distributors.search');
    Route::get('api/distributors/select', [DistributorController::class, 'select'])->name('distributors.select');
});

// Admin & Manager routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    // Product management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])
        ->name('products.adjust-stock');

    // Employee management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])
        ->name('employees.toggle-status');
    Route::post('employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])
        ->name('employees.reset-password');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
        Route::get('/products', [ReportController::class, 'productReport'])->name('products');
        Route::get('/low-stock', [ReportController::class, 'lowStockReport'])->name('low-stock');
        Route::get('/stock-movements', [ReportController::class, 'stockMovementReport'])->name('stock-movements');
        Route::get('/employees', [ReportController::class, 'employeeReport'])->name('employees');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/sales/export', [ReportController::class, 'exportSales'])->name('sales.export');
    });
});

// API routes accessible by all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('api/products/by-barcode/{barcode}', [ProductController::class, 'getByBarcode'])
        ->name('products.by-barcode');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->group(function () {
    // Order approvals
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{order}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{order}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{order}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
});

// Cashier routes (Admin, Manager, Cashier can access)
Route::middleware(['auth', 'role:admin,manager,cashier'])->group(function () {
    // Point of Sale
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/store', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::post('/{sale}/print', [SaleController::class, 'printReceipt'])->name('print');
        Route::get('/{sale}/receipt', [SaleController::class, 'receipt'])->name('receipt');
        Route::post('/{sale}/void', [SaleController::class, 'voidSale'])->name('void');
    });

    // Sales management
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
    Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::post('sales/{sale}/print', [SaleController::class, 'printReceipt'])->name('sales.print');
    Route::post('sales/{sale}/void', [SaleController::class, 'voidSale'])->name('sales.void');
});

require __DIR__.'/auth.php';
