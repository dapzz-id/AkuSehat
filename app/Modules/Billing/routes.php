<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Billing\Subscription\Controllers\BillingController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    // Billing & Perpanjangan Layanan
    Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('billing/google-pay', [BillingController::class, 'processGooglePay'])->name('billing.google-pay');
    Route::post('billing/manual-transfer', [BillingController::class, 'processManualTransfer'])->name('billing.manual-transfer');
    Route::post('billing/midtrans/snap-token', [BillingController::class, 'createMidtransSnap'])->name('billing.midtrans.snap-token');
    Route::get('billing/midtrans/status/{orderId}', [BillingController::class, 'checkMidtransStatus'])->name('billing.midtrans.status');
    Route::post('billing/transactions/{id}/switch-method', [BillingController::class, 'switchPaymentMethod'])->name('billing.switch-method');
    Route::post('billing/transactions/{id}/cancel', [BillingController::class, 'cancelTransaction'])->name('billing.cancel');
    Route::get('billing/invoice/{id}/pdf', [BillingController::class, 'downloadInvoicePdf'])->name('billing.invoice.pdf');
});

// Midtrans Webhook Notification Callback (No Auth, CSRF-exempt)
Route::post('billing/midtrans/notification', [BillingController::class, 'handleMidtransNotification'])->name('billing.midtrans.notification');

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('billing', [BillingController::class, 'superAdminIndex'])->name('billing.index');
    Route::post('billing/payment-methods/{methodKey}/toggle', [BillingController::class, 'togglePaymentMethod'])->name('billing.payment-methods.toggle');
    Route::post('billing/payment-methods/{methodKey}/update', [BillingController::class, 'updatePaymentMethodSetting'])->name('billing.payment-methods.update');
    Route::post('billing/transactions/{id}/approve', [BillingController::class, 'approveTransaction'])->name('billing.approve');
    Route::post('billing/transactions/{id}/reject', [BillingController::class, 'rejectTransaction'])->name('billing.reject');
    Route::post('billing/transactions/{id}/cancel', [BillingController::class, 'cancelTransaction'])->name('billing.cancel');
    Route::get('billing/invoice/{id}/pdf', [BillingController::class, 'downloadInvoicePdf'])->name('billing.invoice.pdf');
});
