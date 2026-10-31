<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Divisi\UnitManagement\Controllers\DivisiController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('divisi')->name('divisi.')->group(function () {
        Route::delete('mass_destroy', [DivisiController::class, 'mass_destroy'])->name('mass_destroy');
        Route::post('{divisi}/clone', [DivisiController::class, 'clone'])->name('clone');
        Route::post('{divisi}/permissions', [DivisiController::class, 'updatePermissions'])->name('permissions');
    });
    Route::resource('divisi', DivisiController::class)->parameters([
        'divisi' => 'divisi'
    ])->only(['index', 'store', 'update', 'destroy']);
});
