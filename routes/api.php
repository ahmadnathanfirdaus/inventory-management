<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsReceivedController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API routes for goods received
Route::middleware('auth')->group(function () {
    Route::get('/available-orders', [GoodsReceivedController::class, 'getAvailableOrders']);
});
