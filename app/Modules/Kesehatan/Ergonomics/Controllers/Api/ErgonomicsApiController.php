<?php

namespace App\Modules\Kesehatan\Ergonomics\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErgonomicsLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErgonomicsApiController extends Controller
{
    public function getToday(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $log = ErgonomicsLog::where('user_id', $user->id)
            ->whereDate('log_date', $today)
            ->first();

        if (!$log) {
            $log = [
                'id' => null,
                'user_id' => $user->id,
                'log_date' => $today->format('Y-m-d'),
                'sedentary_minutes' => 180, // Default 3 jam
                'breaks_taken' => 2,
                'posture_complaint' => null,
                'score' => 85,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    public function saveToday(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sedentary_minutes' => 'required|integer|min:0|max:1440',
            'breaks_taken' => 'nullable|integer|min:0|max:50',
            'posture_complaint' => 'nullable|string|max:100',
            'score' => 'nullable|integer|min:0|max:100',
        ]);

        $user = $request->user();
        $today = Carbon::today();

        $sedentary = (int) $validated['sedentary_minutes'];
        $breaks = isset($validated['breaks_taken']) ? (int) $validated['breaks_taken'] : 0;
        
        // Auto compute score if not provided:
        // ideal: <= 120 mins sedentary with >= 4 breaks = 100
        $score = $validated['score'] ?? max(30, min(100, 100 - (int) max(0, ($sedentary - 120) / 6) + ($breaks * 6)));

        $log = ErgonomicsLog::updateOrCreate(
            [
                'user_id' => $user->id,
                'log_date' => $today,
            ],
            [
                'instansi_id' => $user->instansi_id,
                'sedentary_minutes' => $sedentary,
                'breaks_taken' => $breaks,
                'posture_complaint' => $validated['posture_complaint'] ?? null,
                'score' => $score,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log ergonomi berhasil disimpan',
            'data' => $log,
        ]);
    }

    public function takeBreak(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $log = ErgonomicsLog::firstOrCreate(
            [
                'user_id' => $user->id,
                'log_date' => $today,
            ],
            [
                'instansi_id' => $user->instansi_id,
                'sedentary_minutes' => 180,
                'breaks_taken' => 0,
                'score' => 75,
            ]
        );

        $log->increment('breaks_taken');
        $log->score = min(100, $log->score + 5);
        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'Peregangan berhasil dicatat! Pertahankan postur sehat.',
            'data' => $log,
        ]);
    }
}
