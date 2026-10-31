<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Divisi\UnitManagement\Controllers\Api\DivisiApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/divisi', [DivisiApiController::class, 'getAllDivisi']);
});
