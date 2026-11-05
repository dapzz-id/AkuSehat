<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SuperAdmin\SystemConfig\Controllers\Api\SoftwareUpdateApiController;
use App\Modules\SuperAdmin\AuditLog\Controllers\Api\CrashReportApiController;

// Public Software Update & Patch Notes API (Throttled)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/app-update/check', [SoftwareUpdateApiController::class, 'checkUpdate']);
    Route::get('/app-update/latest-web', [SoftwareUpdateApiController::class, 'latestWebVersion']);
    Route::get('/app-update/history', [SoftwareUpdateApiController::class, 'history']);
});

// Mobile App Crash Reports & Diagnostics Ingestion API
Route::middleware(['throttle:30,1'])->group(function () {
    Route::post('/crash-reports', [CrashReportApiController::class, 'report']);
});
