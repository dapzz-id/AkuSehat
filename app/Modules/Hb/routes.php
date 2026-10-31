<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Hb\PemeriksaanHb\Controllers\HbController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('hb/export/excel', [HbController::class, 'exportExcel'])->name('hb.export.excel');
    Route::get('hb/export/pdf', [HbController::class, 'exportPdf'])->name('hb.export.pdf');
    Route::prefix('hb')->name('hb.')->group(function () {
        Route::delete('mass_destroy', [HbController::class, 'mass_destroy'])->name('mass_destroy');
    });
    Route::resource('hb', HbController::class)->except(['show']);
});
