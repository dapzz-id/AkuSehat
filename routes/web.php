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
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin PMR Routes
    Route::middleware(['role:Admin PMR'])->prefix('admin-pmr')->group(function () {
        Route::get('/kesehatan', [KesehatanController::class, 'index'])->name('admin.kesehatan.index');
        Route::get('/kesehatan/create', [KesehatanController::class, 'create'])->name('admin.kesehatan.create');
        Route::post('/kesehatan', [KesehatanController::class, 'store'])->name('admin.kesehatan.store');
        Route::get('/kesehatan/{id}/edit', [KesehatanController::class, 'edit'])->name('admin.kesehatan.edit');
        Route::put('/kesehatan/{id}', [KesehatanController::class, 'update'])->name('admin.kesehatan.update');
        Route::delete('/kesehatan/{id}', [KesehatanController::class, 'destroy'])->name('admin.kesehatan.destroy');
        
        Route::get('/hb', [HbController::class, 'index'])->name('admin.hb.index');
        Route::get('/hb/create', [HbController::class, 'create'])->name('admin.hb.create');
        Route::post('/hb', [HbController::class, 'store'])->name('admin.hb.store');
        Route::get('/hb/{id}/edit', [HbController::class, 'edit'])->name('admin.hb.edit');
        Route::put('/hb/{id}', [HbController::class, 'update'])->name('admin.hb.update');
        Route::delete('/hb/{id}', [HbController::class, 'destroy'])->name('admin.hb.destroy');
        
        Route::get('/siswa', [UserController::class, 'indexSiswa'])->name('admin.siswa.index');
        Route::get('/kelas', [KelasController::class, 'index'])->name('admin.kelas.index');
        Route::post('/kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
    });
    
    // Guru BK Routes
    Route::middleware(['role:Guru BK'])->prefix('guru-bk')->group(function () {
        Route::get('/peminjaman-pita', [PeminjamanPitaController::class, 'index'])->name('guru-bk.peminjaman.index');
        Route::get('/peminjaman-pita/create', [PeminjamanPitaController::class, 'create'])->name('guru-bk.peminjaman.create');
        Route::post('/peminjaman-pita', [PeminjamanPitaController::class, 'store'])->name('guru-bk.peminjaman.store');
        Route::put('/peminjaman-pita/{id}/kembali', [PeminjamanPitaController::class, 'kembalikan'])->name('guru-bk.peminjaman.kembali');
        Route::get('/warning-terlambat', [PeminjamanPitaController::class, 'warningTerlambat'])->name('guru-bk.warning');
    });
});