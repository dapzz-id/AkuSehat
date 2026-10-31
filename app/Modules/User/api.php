<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Auth\Controllers\Api\AuthApiController;
use App\Modules\User\Member\Controllers\Api\MemberApiController;
use App\Modules\User\ManageUser\Controllers\Api\UserApiController;

// Public System & Auth API
Route::get('/system/realtime-sync', [MemberApiController::class, 'systemRealtimeSync']);

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:login');
    Route::post('/register', [AuthApiController::class, 'registerPersonal']);
    Route::post('/auth/google', [AuthApiController::class, 'googleLogin']);
});

// Protected User & Member API
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/check-auth', [AuthApiController::class, 'checkAuth']);
    Route::get('/me', [MemberApiController::class, 'me']);
    Route::get('/me/sync', [MemberApiController::class, 'systemRealtimeSync']);
    Route::get('/me/modules', [MemberApiController::class, 'myModules']);
    Route::get('/user', [MemberApiController::class, 'me']);
    Route::post('/link-instansi', [AuthApiController::class, 'linkInstansi']);
    Route::post('/profile', [MemberApiController::class, 'updateProfile']);
    Route::put('/profile', [MemberApiController::class, 'updateProfile']);
    Route::post('/change-password', [MemberApiController::class, 'changePassword']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    Route::prefix('member')->group(function () {
        Route::get('/', [UserApiController::class, 'getMember']);
        Route::get('/divisi/{divisi_id}', [UserApiController::class, 'getMemberByDivisi']);
        Route::get('/summary', [MemberApiController::class, 'summary']);
        Route::get('/summary/grafik', [MemberApiController::class, 'summaryGrafik']);
    });
});
