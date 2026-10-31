<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Mens\DataHaid\Controllers\FemHealthMonitorController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    // Monitoring Siklus Menstruasi & Peminjaman Pita / Cuti Haid
    Route::get('femhealth', [FemHealthMonitorController::class, 'index'])->name('femhealth.index');
    Route::post('femhealth/peminjaman', [FemHealthMonitorController::class, 'storePita'])->name('femhealth.store_pita');
    Route::post('femhealth/peminjaman/{peminjaman}/verify', [FemHealthMonitorController::class, 'verifyPita'])->name('femhealth.verify_pita');
    Route::post('femhealth/peminjaman/{peminjaman}/return', [FemHealthMonitorController::class, 'returnPita'])->name('femhealth.return_pita');
    Route::post('femhealth/peminjaman/{peminjaman}/sanksi', [FemHealthMonitorController::class, 'updateSanksiDenda'])->name('femhealth.sanksi_pita');
    Route::post('femhealth/haid', [FemHealthMonitorController::class, 'storeHaid'])->name('femhealth.store_haid');
    Route::post('femhealth/haid/{dataHaid}/stop', [FemHealthMonitorController::class, 'stopHaid'])->name('femhealth.stop_haid');
});
