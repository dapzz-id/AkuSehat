<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Billing\Subscription\Controllers\BillingController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Google Play In-App Billing (Android / Play Console)
    Route::post('/subscription/google-play/verify', [BillingController::class, 'processGooglePlayInApp']);
});
