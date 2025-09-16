<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\KesehatanApiController;
use App\Http\Controllers\Api\HbApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\KelasApiController;
use App\Http\Controllers\Api\DashboardApiController;

// Public API Routes
Route::post('/login', [AuthApiController::class, 'login']);

// Protected API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load('kelas');
    });
    
    // Dashboard API untuk Guru Olahraga
    Route::get('/dashboard/guru-olahraga', [DashboardApiController::class, 'guruOlahraga']);
    Route::get('/dashboard/siswa/{id}', [DashboardApiController::class, 'siswaDetail']);
    
    // Kesehatan API untuk Siswa dan Guru Olahraga
    Route::get('/kesehatan/siswa/{id}', [KesehatanApiController::class, 'getKesehantanSiswa']);
    Route::get('/kesehatan/kelas/{id}', [KesehatanApiController::class, 'getKesehatanKelas']);
    Route::get('/kesehatan/statistik/{kelas_id}', [KesehatanApiController::class, 'getStatistikKelas']);
    
    // HB API
    Route::get('/hb/siswa/{id}', [HbApiController::class, 'getHbSiswa']);
    
    // User API
    Route::get('/siswa', [UserApiController::class, 'getSiswa']);
    Route::get('/siswa/kelas/{kelas_id}', [UserApiController::class, 'getSiswaByKelas']);
    
    // Kelas API
    Route::get('/kelas', [KelasApiController::class, 'getAllKelas']);
    
    // AI Recommendation API
    Route::post('/ai/recommendation', [KesehatanApiController::class, 'getAIRecommendation']);
});