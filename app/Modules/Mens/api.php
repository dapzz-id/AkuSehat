<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Mens\DataHaid\Controllers\Api\HealthMonitorApiController;
use App\Modules\User\Member\Controllers\Api\MemberApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Member - Haid & Pita Endpoints
    Route::prefix('member')->group(function () {
        Route::get('/{id}/riwayat-haid', [MemberApiController::class, 'riwayatHaidMember']);
        Route::get('/status-pita', [MemberApiController::class, 'getStatusPita']);
        Route::get('/check-peminjaman-status', [MemberApiController::class, 'checkPeminjamanStatus']);
        Route::get('/data-haid', [MemberApiController::class, 'listHaid']);
        Route::post('/data-haid', [MemberApiController::class, 'storeHaid']);
        Route::put('/data-haid/{id}', [MemberApiController::class, 'updateHaid']);
        Route::get('/{id}/riwayat-pita', [MemberApiController::class, 'getRiwayatPita']);
        Route::post('/peminjaman-pita', [MemberApiController::class, 'storePita']);
    });

    // Health Monitor - Operasional UKS & Monitoring Haid/Pita
    Route::prefix('health-monitor')->middleware('role:Health Monitor,Admin,Admin Instansi,SuperAdmin')->group(function () {
        Route::get('/member-haid', [HealthMonitorApiController::class, 'getMemberSedangHaid']);
        Route::get('/', [HealthMonitorApiController::class, 'index']);
        Route::put('/{id}/accept-verifikasi', [HealthMonitorApiController::class, 'acceptVerifikasi']);
        Route::put('/{id}/reject-verifikasi', [HealthMonitorApiController::class, 'rejectVerifikasi']);
        Route::get('/warning', [HealthMonitorApiController::class, 'warningTerlambat']);
        Route::get('/{id}', [HealthMonitorApiController::class, 'show']);
        Route::post('/', [HealthMonitorApiController::class, 'store']);
        Route::put('/{id}', [HealthMonitorApiController::class, 'update']);
        Route::delete('/{id}', [HealthMonitorApiController::class, 'destroy']);
    });
});
