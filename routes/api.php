<?php

use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaApiController;
use App\Http\Controllers\Api\GuruOlahragaApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\KesehatanApiController;
use App\Http\Controllers\Api\HbApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\KelasApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\GuruBkApiController;

// Public API Routes
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

Route::get('/load-kelas-for-register', function () {
    return response()->json([
        'status' => true,
        'message' => 'Data kelas berhasil dimuat.',
        'data' => Kelas::select('id', 'kelas', 'jurusan')->get()
    ]);
});

// Protected API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    
    // Siswa
    Route::get('/me', [SiswaApiController::class, 'me']);
    Route::get('/user', [SiswaApiController::class, 'me']);
    Route::get('/notifications', [SiswaApiController::class, 'notifications']);


    Route::get('/siswa/summary', [SiswaApiController::class, 'summary']);
    Route::get('/siswa/summary/grafik', [SiswaApiController::class, 'summaryGrafik']);
    Route::get('/siswa/data-haid', [SiswaApiController::class, 'listHaid']);
    Route::post('/siswa/data-haid', [SiswaApiController::class, 'storeHaid']);
    Route::post('/siswa/peminjaman-pita', [SiswaApiController::class, 'storePita']);
    Route::get('/siswa/kelas/{kelas_id}', [UserApiController::class, 'getSiswaByKelas']);
    Route::get('/siswa', [UserApiController::class, 'getSiswa']);
    Route::get('/siswa/{id}/kesehatan', [KesehatanApiController::class, 'getKesehatanSiswa']);
    Route::get('/siswa/{id}/hb', [HbApiController::class, 'getHbSiswa']);
    Route::get('/siswa/{id}/riwayat-haid', [SiswaApiController::class, 'riwayatHaidSiswa']);
    Route::post('/change-password', [SiswaApiController::class, 'changePassword']);
    Route::get('/siswa/status-pita', [SiswaApiController::class, 'getStatusPita']);

    // Guru Olahraga
    Route::get('/guru/health-summary', [GuruOlahragaApiController::class, 'healthSummary']);
    Route::get('/guru/health-by-class', [GuruOlahragaApiController::class, 'healthByClass']);
    Route::get('/guru/health-notification', [GuruOlahragaApiController::class, 'healthNotification']);

    // Guru BK
    Route::prefix('peminjaman-pita')->group(function () {
        Route::get('/', [GuruBkApiController::class, 'index']);
        Route::put('/{id}/accept-verifikasi', [GuruBkApiController::class, 'acceptVerifikasi']);
        Route::put('/{id}/reject-verifikasi', [GuruBkApiController::class, 'rejectVerifikasi']);
        Route::get('/warning', [GuruBkApiController::class, 'warningTerlambat']);
        Route::get('/{id}', [GuruBkApiController::class, 'show']);
        Route::post('/', [GuruBkApiController::class, 'store']);
        Route::put('/{id}', [GuruBkApiController::class, 'update']);
        Route::delete('/{id}', [GuruBkApiController::class, 'destroy']);
    });

    Route::get('/users/siswi-haid', [GuruBkApiController::class, 'getSiswiSedangHaid']);
});