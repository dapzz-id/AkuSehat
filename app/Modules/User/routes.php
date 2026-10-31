<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Auth\Controllers\AuthController;
use App\Modules\User\Member\Controllers\ProfileController;
use App\Modules\User\ManageUser\Controllers\AdminUserController;
use App\Modules\User\ManageUser\Controllers\SuperAdminUserController;

// 1. Authentication Routes (Web)
Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// 2. Authenticated Auth & Profile Operations
Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('link-instansi', [AuthController::class, 'linkInstansi'])->name('link.instansi');

    // Profil Pengguna (Semua Role)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});

// 3. Admin Portal - User Management
Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('template', [AdminUserController::class, 'downloadTemplate'])->name('template');
        Route::post('import', [AdminUserController::class, 'importUsers'])->name('import');
        Route::get('export/excel', [AdminUserController::class, 'exportExcel'])->name('export.excel');
        Route::patch('mass_update_divisi', [AdminUserController::class, 'mass_update_divisi'])->name('mass_update_divisi');
        Route::patch('mass_remove_divisi', [AdminUserController::class, 'mass_remove_divisi'])->name('mass_remove_divisi');
        Route::delete('mass_destroy', [AdminUserController::class, 'mass_destroy'])->name('mass_destroy');
        Route::post('{user}/permissions', [AdminUserController::class, 'updatePermissions'])->name('permissions');
    });
    Route::resource('users', AdminUserController::class);
});

// 4. SuperAdmin Portal - User Management
Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::post('users/{user}/ban', [SuperAdminUserController::class, 'ban'])->name('users.ban');
    Route::post('users/{user}/suspend', [SuperAdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/activate', [SuperAdminUserController::class, 'activate'])->name('users.activate');
    Route::resource('users', SuperAdminUserController::class);
});
