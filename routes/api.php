<?php

use Illuminate\Http\Request;
use App\Models\Divisi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberApiController;
use App\Http\Controllers\Api\HealthConsultantApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\KesehatanApiController;
use App\Http\Controllers\Api\HbApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\DivisiApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\HealthMonitorApiController;

// Public API Routes
Route::post('/login', [AuthApiController::class, 'login']);

// Protected API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Common Routes
    Route::get('/check-auth', [AuthApiController::class, 'checkAuth']);
    Route::get('/me', [MemberApiController::class, 'me']);
    Route::get('/user', [MemberApiController::class, 'me']);
    Route::post('/change-password', [MemberApiController::class, 'changePassword']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Member
    Route::prefix('member')->group(function () {
        Route::get('/', [UserApiController::class, 'getMember']);
        Route::get('/{id}/kesehatan', [KesehatanApiController::class, 'getKesehatanMember']);
        Route::get('/{id}/hb', [HbApiController::class, 'getHbMember']);
        Route::get('/{id}/riwayat-haid', [MemberApiController::class, 'riwayatHaidMember']);
        Route::get('/status-pita', [MemberApiController::class, 'getStatusPita']);
        Route::get('/check-peminjaman-status', [MemberApiController::class, 'checkPeminjamanStatus']);
        Route::get('/summary', [MemberApiController::class, 'summary']);
        Route::get('/summary/grafik', [MemberApiController::class, 'summaryGrafik']);
        Route::get('/data-haid', [MemberApiController::class, 'listHaid']);
        Route::post('/data-haid', [MemberApiController::class, 'storeHaid']);
        Route::put('/data-haid/{id}', [MemberApiController::class, 'updateHaid']);
        Route::get('/{id}/riwayat-pita', [MemberApiController::class, 'getRiwayatPita']);
        Route::post('/peminjaman-pita', [MemberApiController::class, 'storePita']);
        Route::get('/divisi/{divisi_id}', [UserApiController::class, 'getMemberByDivisi']);
    });

    // Health Consultant
    Route::prefix('health-consultant')->group(function () {
        Route::get('/health-summary', [HealthConsultantApiController::class, 'healthSummary']);
        Route::get('/health-by-division', [HealthConsultantApiController::class, 'healthByDivision']);
        Route::get('/health-notification', [HealthConsultantApiController::class, 'healthNotification']);
    });

    // Health Monitor
    Route::prefix('health-monitor')->group(function () {
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