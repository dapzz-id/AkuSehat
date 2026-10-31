<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SuperAdmin\SystemConfig\Controllers\Api\SoftwareUpdateApiController;

// Public Software Update & Patch Notes API (Throttled)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/app-update/check', [SoftwareUpdateApiController::class, 'checkUpdate']);
    Route::get('/app-update/latest-web', [SoftwareUpdateApiController::class, 'latestWebVersion']);
    Route::get('/app-update/history', [SoftwareUpdateApiController::class, 'history']);
});
