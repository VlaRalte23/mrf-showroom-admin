<?php

use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileDebtController;
use App\Http\Controllers\Api\MobileSalesController;
use App\Http\Controllers\Api\MobileStockReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::get('/dashboard', [MobileDashboardController::class, 'index']);
    Route::get('/stock-report', [MobileStockReportController::class, 'index']);
    Route::get('/debts', [MobileDebtController::class, 'index']);
    Route::get('/sales', [MobileSalesController::class, 'index']);
});
