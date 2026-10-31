<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SuperAdmin\Instansi\Controllers\InstansiController;
use App\Modules\SuperAdmin\LicenseKey\Controllers\LicenseKeyController;
use App\Modules\SuperAdmin\SystemConfig\Controllers\SoftwareAppController;
use App\Modules\SuperAdmin\MaintenanceMode\Controllers\MaintenanceModeController;
use App\Modules\SuperAdmin\AuditLog\Controllers\AuditLogController;
use App\Modules\SuperAdmin\ModulePermission\Controllers\SuperAdminModuleController;
use App\Modules\SuperAdmin\Terminal\Controllers\TerminalController;

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('instansi/kabupaten', [InstansiController::class, 'getKabupaten'])->name('instansi.kabupaten');
    Route::resource('instansi', InstansiController::class)->except(['show']);
    Route::put('license/{license}/renew', [LicenseKeyController::class, 'renew'])->name('license.renew');
    Route::resource('license', LicenseKeyController::class);
    Route::get('update/{id}/download', [SoftwareAppController::class, 'downloadApp'])->name('update.download');
    Route::resource('update', SoftwareAppController::class)->except(['show']);
    Route::post('maintenance/{id}/toggle', [MaintenanceModeController::class, 'toggleUpdate'])->name('maintenance.toggle');
    Route::resource('maintenance', MaintenanceModeController::class)->only(['index']);

    // Audit Logs & Aktivitas Sistem
    Route::get('logs', [AuditLogController::class, 'index'])->name('logs.index');

    // Master Modul & Kontrol Fitur Global
    Route::get('modules', [SuperAdminModuleController::class, 'index'])->name('modules.index');
    Route::put('modules', [SuperAdminModuleController::class, 'update'])->name('modules.update');
    Route::post('modules/tester/{id}', [SuperAdminModuleController::class, 'toggleTester'])->name('modules.tester.toggle');

    // Web Terminal & Konsol Laravel
    Route::get('terminal', [TerminalController::class, 'index'])->name('terminal.index');
    Route::post('terminal/execute', [TerminalController::class, 'execute'])->name('terminal.execute');
    Route::post('terminal/save-file', [TerminalController::class, 'saveFile'])->name('terminal.save-file');
});
