<?php

use App\Http\Controllers\Api\MobileAttendanceController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileDebtController;
use App\Http\Controllers\Api\MobileSalesController;
use App\Http\Controllers\Api\MobileStockReportController;
use App\Http\Middleware\EnsureMobileApiTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);

    Route::middleware([EnsureMobileApiTokenIsValid::class])->group(function () {
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/dashboard', [MobileDashboardController::class, 'index']);
        Route::get('/stock-report', [MobileStockReportController::class, 'index']);
        Route::get('/debts', [MobileDebtController::class, 'index']);
        Route::get('/sales', [MobileSalesController::class, 'index']);
        Route::get('/attendance', [MobileAttendanceController::class, 'index']);
        Route::post('/attendance', [MobileAttendanceController::class, 'store']);
    });
});
