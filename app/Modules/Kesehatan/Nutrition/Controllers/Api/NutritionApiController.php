<?php

namespace App\Modules\Kesehatan\Nutrition\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NutritionLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionApiController extends Controller
{
    public function getToday(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $logs = NutritionLog::where('user_id', $user->id)
            ->whereDate('log_date', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        $totalCalories = $logs->sum('calories');
        $balancedCount = $logs->where('is_balanced', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
                'total_calories' => $totalCalories,
                'balanced_count' => $balancedCount,
                'target_calories' => 2000,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meal_type' => 'required|string|in:sarapan,makan_siang,makan_malam,camilan',
            'food_name' => 'required|string|max:200',
            'calories' => 'required|integer|min:0|max:5000',
            'is_balanced' => 'nullable|boolean',
            'notes' => 'nullable|string|max:300',
        ]);

        $user = $request->user();
        $log = NutritionLog::create([
            'user_id' => $user->id,
            'instansi_id' => $user->instansi_id,
            'log_date' => Carbon::today(),
            'meal_type' => $validated['meal_type'],
            'food_name' => $validated['food_name'],
            'calories' => (int) $validated['calories'],
            'is_balanced' => $validated['is_balanced'] ?? true,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan menu makan berhasil disimpan',
            'data' => $log,
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $log = NutritionLog::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan nutrisi tidak ditemukan',
            ], 404);
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan nutrisi berhasil dihapus',
        ]);
    }
}
