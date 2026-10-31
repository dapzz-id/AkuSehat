<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Kesehatan\Pemeriksaan\Controllers\KesehatanController;
use App\Modules\Kesehatan\Activities\Controllers\ActivityMonitorController;
use App\Modules\Kesehatan\VitalLogs\Controllers\VitalMonitorController;
use App\Modules\Kesehatan\Counseling\Controllers\CounselingMonitorController;
use App\Modules\Kesehatan\Nutrition\Controllers\NutritionMonitorController;
use App\Modules\Kesehatan\Burnout\Controllers\BurnoutMonitorController;
use App\Modules\Kesehatan\Ergonomics\Controllers\ErgonomicsMonitorController;
use App\Modules\Kesehatan\Vaccine\Controllers\VaccineMonitorController;
use App\Modules\SuperAdmin\ModulePermission\Controllers\ModulePermissionController;

Route::middleware(['auth', 'role:Admin,Admin Instansi,Health Monitor,Health Consultant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('kesehatan/export/excel', [KesehatanController::class, 'exportExcel'])->name('kesehatan.export.excel');
    Route::get('kesehatan/export/pdf', [KesehatanController::class, 'exportPdf'])->name('kesehatan.export.pdf');
    Route::prefix('kesehatan')->name('kesehatan.')->group(function () {
        Route::delete('mass_destroy', [KesehatanController::class, 'mass_destroy'])->name('mass_destroy');
    });
    Route::resource('kesehatan', KesehatanController::class)->except(['show']);

    // Monitoring Kebugaran & Aktivitas Olahraga
    Route::get('activities', [ActivityMonitorController::class, 'index'])->name('activities.index');

    // Monitoring Vitalitas & Hidrasi Harian
    Route::get('vital-logs', [VitalMonitorController::class, 'index'])->name('vital-logs.index');

    // Monitoring Konseling Mental & SafeSpace
    Route::get('konseling', [CounselingMonitorController::class, 'index'])->name('counseling.index');
    Route::post('konseling', [CounselingMonitorController::class, 'store'])->name('counseling.store');
    Route::post('konseling/{counseling}/approve', [CounselingMonitorController::class, 'approve'])->name('counseling.approve');
    Route::post('konseling/{counseling}/reject', [CounselingMonitorController::class, 'reject'])->name('counseling.reject');
    Route::post('konseling/{counseling}/status', [CounselingMonitorController::class, 'updateStatus'])->name('counseling.status');

    // Monitoring Nutrisi & Pola Makan Sehat
    Route::get('nutrisi', [NutritionMonitorController::class, 'index'])->name('nutrition.index');

    // Monitoring Skrining Stres & Burnout
    Route::get('burnout', [BurnoutMonitorController::class, 'index'])->name('burnout.index');

    // Monitoring Ergonomi & Postur Kerja
    Route::get('ergonomi', [ErgonomicsMonitorController::class, 'index'])->name('ergonomics.index');

    // Monitoring Paspor Vaksinasi & Imunisasi
    Route::get('vaksinasi', [VaccineMonitorController::class, 'index'])->name('vaccine.index');

    // Kelola Modul & Hak Akses Instansi
    Route::get('modules', [ModulePermissionController::class, 'index'])->name('modules.index');
    Route::post('modules', [ModulePermissionController::class, 'update'])->name('modules.update');
});
