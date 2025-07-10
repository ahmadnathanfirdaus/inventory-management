<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\GoodsReceivedController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Orders management
    Route::resource('orders', OrderController::class);

    // Goods received management
    Route::resource('goods-received', GoodsReceivedController::class);
    Route::get('goods-received/po-details', [GoodsReceivedController::class, 'getPODetails'])
        ->name('goods-received.po-details');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->group(function () {
    // Order approvals
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{order}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{order}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{order}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
