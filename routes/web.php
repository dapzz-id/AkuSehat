<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Public Legal & Privacy Policy
Route::get('/kebijakan-privasi', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

// Public Account & Data Deletion (Google Play Compliance)
Route::get('/hapus-akun', function () {
    return view('delete-account');
})->name('account.delete');

Route::get('/delete-account', function () {
    return view('delete-account');
});

// Protected Shared Base Routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [\App\Modules\User\ManageUser\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Riwayat Pembaruan & Catatan Patch (Akses semua pengguna)
    Route::get('riwayat-update', [\App\Modules\SuperAdmin\SystemConfig\Controllers\PatchNoteController::class, 'index'])->name('patch_notes.index');
    Route::get('version-check/web', [\App\Modules\SuperAdmin\SystemConfig\Controllers\Api\SoftwareUpdateApiController::class, 'latestWebVersion'])->name('version.check.web');
});

// ==========================================
// MODULAR ROUTES (Domain/Feature-Driven)
// ==========================================
require base_path('app/Modules/User/routes.php');
require base_path('app/Modules/Mens/routes.php');
require base_path('app/Modules/Kesehatan/routes.php');
require base_path('app/Modules/Hb/routes.php');
require base_path('app/Modules/Divisi/routes.php');
require base_path('app/Modules/Billing/routes.php');
require base_path('app/Modules/SuperAdmin/routes.php');