<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Hb\PemeriksaanHb\Controllers\Api\HbApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/member/{id}/hb', [HbApiController::class, 'getHbMember']);
});
