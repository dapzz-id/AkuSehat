<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KesehatanController;
use App\Http\Controllers\HbController;
use App\Http\Controllers\PeminjamanPitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin PMR Routes
    Route::middleware(['role:Admin PMR'])->prefix('admin-pmr')->name('admin.')->group(function () {
        Route::resource('kesehatan', KesehatanController::class);
        Route::resource('hb', HbController::class);
        Route::resource('siswa', UserController::class);
        Route::resource('kelas', KelasController::class)->parameters([
            'kelas' => 'kelas'
        ]);
    });
    
    // Guru BK Routes
    Route::middleware(['role:Guru BK'])->prefix('guru-bk')->name('guru-bk.')->group(function () {
        Route::put('peminjaman-pita/{id}/kembali', [PeminjamanPitaController::class, 'kembalikan'])->name('peminjaman.kembali');
        Route::get('peminjaman-pita/warning-terlambat', [PeminjamanPitaController::class, 'warningTerlambat'])->name('warning');
        Route::resource('peminjaman-pita', PeminjamanPitaController::class);
    });
});