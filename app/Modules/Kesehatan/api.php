<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Kesehatan\Pemeriksaan\Controllers\Api\KesehatanApiController;
use App\Modules\Kesehatan\Activities\Controllers\Api\ActivityApiController;
use App\Modules\Kesehatan\VitalLogs\Controllers\Api\VitalLogApiController;
use App\Modules\Kesehatan\Counseling\Controllers\Api\HealthConsultantApiController;
use App\Modules\User\Member\Controllers\Api\MemberApiController;

use App\Modules\Kesehatan\Ergonomics\Controllers\Api\ErgonomicsApiController;
use App\Modules\Kesehatan\Burnout\Controllers\Api\BurnoutApiController;
use App\Modules\Kesehatan\Nutrition\Controllers\Api\NutritionApiController;
use App\Modules\Kesehatan\Vaccine\Controllers\Api\VaccineApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    // 1. Rekam Medis Fisik Member
    Route::get('/member/{id}/kesehatan', [KesehatanApiController::class, 'getKesehatanMember']);

    // 2. Konseling Mental & SafeSpace (Member Booking)
    Route::prefix('member')->group(function () {
        Route::get('/counseling', [MemberApiController::class, 'listCounseling']);
        Route::post('/counseling/book', [MemberApiController::class, 'bookCounseling']);
        Route::put('/counseling/{id}/cancel', [MemberApiController::class, 'cancelCounseling']);
    });

    // 3. Health Consultant Portal
    Route::prefix('health-consultant')->middleware('role:Health Consultant,Admin,Admin Instansi,SuperAdmin')->group(function () {
        Route::get('/health-summary', [HealthConsultantApiController::class, 'healthSummary']);
        Route::get('/health-by-division', [HealthConsultantApiController::class, 'healthByDivision']);
        Route::get('/health-notification', [HealthConsultantApiController::class, 'healthNotification']);
        Route::get('/counseling', [HealthConsultantApiController::class, 'listCounseling']);
        Route::put('/counseling/{id}/approve', [HealthConsultantApiController::class, 'approveCounseling']);
        Route::put('/counseling/{id}/reject', [HealthConsultantApiController::class, 'rejectCounseling']);
        Route::put('/counseling/{id}/complete', [HealthConsultantApiController::class, 'completeCounseling']);
    });

    // 4. Health Monitor Konseling & Rujukan
    Route::prefix('health-monitor')->middleware('role:Health Monitor,Admin,Admin Instansi,SuperAdmin')->group(function () {
        Route::get('/counseling', [HealthConsultantApiController::class, 'listCounseling']);
        Route::put('/counseling/{id}/approve', [HealthConsultantApiController::class, 'approveCounseling']);
        Route::put('/counseling/{id}/reject', [HealthConsultantApiController::class, 'rejectCounseling']);
    });

    // 5. Activities (GPS Trackers, Running, Walking, Cycling)
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityApiController::class, 'index']);
        Route::post('/', [ActivityApiController::class, 'store']);
        Route::get('/leaderboard', [ActivityApiController::class, 'leaderboard']);
        Route::get('/leaderboard/podium', [ActivityApiController::class, 'podium']);
        Route::get('/leaderboard/runners', [ActivityApiController::class, 'runners']);
        Route::get('/{id}', [ActivityApiController::class, 'show']);
        Route::delete('/{id}', [ActivityApiController::class, 'destroy']);
    });

    // 6. Daily Vital Logs (Hydration, Sleep, Mood, BP)
    Route::prefix('vital-logs')->group(function () {
        Route::get('/today', [VitalLogApiController::class, 'getToday']);
        Route::post('/water/add', [VitalLogApiController::class, 'addWater']);
        Route::post('/', [VitalLogApiController::class, 'updateToday']);
        Route::get('/history', [VitalLogApiController::class, 'history']);
    });

    // 7. Ergonomics & Sedentary Logs
    Route::prefix('ergonomics')->group(function () {
        Route::get('/today', [ErgonomicsApiController::class, 'getToday']);
        Route::post('/', [ErgonomicsApiController::class, 'saveToday']);
        Route::post('/break', [ErgonomicsApiController::class, 'takeBreak']);
    });

    // 8. Burnout & Mental Stress Screening
    Route::prefix('burnout')->group(function () {
        Route::get('/latest', [BurnoutApiController::class, 'getLatest']);
        Route::post('/', [BurnoutApiController::class, 'submitScreening']);
    });

    // 9. Nutrition & Meal Tracker
    Route::prefix('nutrition')->group(function () {
        Route::get('/today', [NutritionApiController::class, 'getToday']);
        Route::post('/', [NutritionApiController::class, 'store']);
        Route::delete('/{id}', [NutritionApiController::class, 'destroy']);
    });

    // 10. Vaccine & Immunization Passport
    Route::prefix('vaccine')->group(function () {
        Route::get('/', [VaccineApiController::class, 'index']);
        Route::post('/', [VaccineApiController::class, 'store']);
        Route::delete('/{id}', [VaccineApiController::class, 'destroy']);
    });
});

