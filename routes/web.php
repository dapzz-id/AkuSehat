<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KesehatanController;
use App\Http\Controllers\HbController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\SuperAdmin\InstansiController;
use App\Http\Controllers\SuperAdmin\LicenseKeyController;
use App\Http\Controllers\SuperAdmin\SoftwareAppController;
use App\Http\Controllers\SuperAdmin\MaintenanceModeController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
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
    
    // Admin Routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('kesehatan/export/excel', [KesehatanController::class, 'exportExcel'])->name('kesehatan.export.excel');
        Route::get('kesehatan/export/pdf', [KesehatanController::class, 'exportPdf'])->name('kesehatan.export.pdf');
        Route::prefix('kesehatan')->name('kesehatan.')->group(function () {
            Route::delete('mass_destroy', [KesehatanController::class, 'mass_destroy'])->name('mass_destroy');
        });
        Route::resource('kesehatan', KesehatanController::class);

        Route::get('hb/export/excel', [HbController::class, 'exportExcel'])->name('hb.export.excel');
        Route::get('hb/export/pdf', [HbController::class, 'exportPdf'])->name('hb.export.pdf');
        Route::prefix('hb')->name('hb.')->group(function () {
            Route::delete('mass_destroy', [HbController::class, 'mass_destroy'])->name('mass_destroy');
        });
        Route::resource('hb', HbController::class);

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('template', [UserController::class, 'downloadTemplate'])->name('template');
            Route::post('import', [UserController::class, 'importUsers'])->name('import');
            Route::get('export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
            Route::patch('mass_update_divisi', [UserController::class, 'mass_update_divisi'])->name('mass_update_divisi');
            Route::delete('mass_destroy', [UserController::class, 'mass_destroy'])->name('mass_destroy');
        });
        Route::resource('users', UserController::class);
        
        Route::prefix('divisi')->name('divisi.')->group(function () {
            Route::delete('mass_destroy', [DivisiController::class, 'mass_destroy'])->name('mass_destroy');
        });
        Route::resource('divisi', DivisiController::class)->parameters([
            'divisi' => 'divisi'
        ]);
    });

    // SuperAdmin Routes
    Route::middleware(['role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('instansi/kabupaten', [InstansiController::class, 'getKabupaten'])->name('instansi.kabupaten');
        Route::resource('instansi', InstansiController::class);
        Route::resource('license', LicenseKeyController::class);
        Route::get('update/{id}/download', [SoftwareAppController::class, 'downloadApp'])->name('update.download');
        Route::resource('update', SoftwareAppController::class);
        Route::post('maintenance/{id}/toggle', [MaintenanceModeController::class, 'toggleUpdate'])->name('maintenance.toggle');
        Route::resource('maintenance', MaintenanceModeController::class);
        Route::resource('users', SuperAdminUserController::class);
    });
});