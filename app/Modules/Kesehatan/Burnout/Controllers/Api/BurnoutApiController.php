<?php

namespace App\Modules\Kesehatan\Burnout\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BurnoutScreening;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BurnoutApiController extends Controller
{
    public function getLatest(Request $request): JsonResponse
    {
        $user = $request->user();
        $latest = BurnoutScreening::where('user_id', $user->id)
            ->latest('screening_date')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $latest,
        ]);
    }

    public function submitScreening(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stress_score' => 'required|integer|min:0|max:100',
            'workload_rating' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:500',
        ]);

        $score = (int) $validated['stress_score'];
        $level = match (true) {
            $score <= 35 => 'rendah',
            $score <= 70 => 'sedang',
            default => 'tinggi',
        };

        $user = $request->user();
        $screening = BurnoutScreening::create([
            'user_id' => $user->id,
            'instansi_id' => $user->instansi_id,
            'screening_date' => Carbon::today(),
            'stress_score' => $score,
            'burnout_level' => $level,
            'workload_rating' => (int) $validated['workload_rating'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skrining stres & burnout berhasil disimpan',
            'data' => $screening,
        ]);
    }
}
