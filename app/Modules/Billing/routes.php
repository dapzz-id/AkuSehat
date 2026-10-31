<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Billing\Subscription\Controllers\BillingController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    // Billing & Perpanjangan Layanan
    Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('billing/google-pay', [BillingController::class, 'processGooglePay'])->name('billing.google-pay');
    Route::get('billing/invoice/{id}/pdf', [BillingController::class, 'downloadInvoicePdf'])->name('billing.invoice.pdf');
});
