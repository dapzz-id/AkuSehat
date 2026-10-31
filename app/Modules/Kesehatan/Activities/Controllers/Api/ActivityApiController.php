<?php

namespace App\Modules\Kesehatan\Activities\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Activities\Repositories\ActivityRepository;
use App\Modules\Kesehatan\Activities\Services\ActivityService;
use App\Modules\Kesehatan\Activities\Requests\ListActivityRequest;
use App\Modules\Kesehatan\Activities\Requests\StoreActivityRequest;
use App\Modules\Kesehatan\Activities\Requests\ShowActivityRequest;
use App\Modules\Kesehatan\Activities\Requests\DeleteActivityRequest;
use App\Modules\Kesehatan\Activities\Requests\LeaderboardActivityRequest;
use Illuminate\Http\JsonResponse;

class ActivityApiController extends Controller
{
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected ActivityService $activityService
    ) {}

    public function index(ListActivityRequest $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->validated('type');

        $summary = $this->activityRepository->getUserSummary($user->id, $type);
        $activities = $this->activityRepository->getUserActivities(
            $user->id,
            $type,
            (int) $request->validated('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'summary' => [
                'total_activities' => $summary['total_activities'],
                'total_distance_km' => round($summary['total_distance_meters'] / 1000, 2),
                'total_duration_formatted' => $this->formatDuration($summary['total_duration_seconds']),
                'total_duration_seconds' => $summary['total_duration_seconds'],
                'total_calories' => $summary['total_calories'],
                'total_steps' => $summary['total_steps'],
            ],
            'data' => $activities,
        ]);
    }

    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = $this->activityService->recordActivity($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil disimpan',
            'data' => $activity,
        ], 201);
    }

    public function show(ShowActivityRequest $request, string|int $id): JsonResponse
    {
        $activity = $this->activityService->getActivityDetail($request->user(), $id);

        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $activity]);
    }

    public function destroy(DeleteActivityRequest $request, string|int $id): JsonResponse
    {
        $deleted = $this->activityService->deleteActivity($request->user(), $id);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Aktivitas berhasil dihapus']);
    }

    public function leaderboard(LeaderboardActivityRequest $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->validated('period', 'month');
        $scope = $request->validated('scope', 'local');

        // Security: In local mode, strictly enforce user's authenticated instansi_id (IDOR prevention)
        $instansiId = null;
        if ($scope === 'local') {
            $instansiId = $user->instansi_id ?: 'NO_INSTANSI';
        }

        $tipeSebutan = $user->instansi ? $user->instansi->tipe_sebutan : 'Instansi';
        $instansiName = $user->instansi ? $user->instansi->nama_instansi : null;

        $leaders = $this->activityService->getLeaderboard($instansiId, $period, 20, 0, $user->id);

        return response()->json([
            'success' => true,
            'period' => $period,
            'scope' => $scope,
            'tipe_sebutan' => $tipeSebutan,
            'instansi_name' => $instansiName,
            'data' => $leaders,
        ]);
    }

    public function podium(LeaderboardActivityRequest $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->validated('period', 'month');
        $scope = $request->validated('scope', 'local');

        // Security: In local mode, strictly enforce user's authenticated instansi_id (IDOR prevention)
        $instansiId = null;
        if ($scope === 'local') {
            $instansiId = $user->instansi_id ?: 'NO_INSTANSI';
        }

        $tipeSebutan = $user->instansi ? $user->instansi->tipe_sebutan : 'Instansi';
        $instansiName = $user->instansi ? $user->instansi->nama_instansi : null;

        $podium = $this->activityService->getLeaderboard($instansiId, $period, 3, 0, $user->id);

        return response()->json([
            'success' => true,
            'period' => $period,
            'scope' => $scope,
            'tipe_sebutan' => $tipeSebutan,
            'instansi_name' => $instansiName,
            'data' => $podium,
        ]);
    }

    public function runners(LeaderboardActivityRequest $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->validated('period', 'month');
        $scope = $request->validated('scope', 'local');

        // Security: In local mode, strictly enforce user's authenticated instansi_id (IDOR prevention)
        $instansiId = null;
        if ($scope === 'local') {
            $instansiId = $user->instansi_id ?: 'NO_INSTANSI';
        }

        $tipeSebutan = $user->instansi ? $user->instansi->tipe_sebutan : 'Instansi';
        $instansiName = $user->instansi ? $user->instansi->nama_instansi : null;

        $runners = $this->activityService->getLeaderboard($instansiId, $period, 7, 3, $user->id);

        return response()->json([
            'success' => true,
            'period' => $period,
            'scope' => $scope,
            'tipe_sebutan' => $tipeSebutan,
            'instansi_name' => $instansiName,
            'data' => $runners,
        ]);
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02dj %02dm %02dd', $hours, $minutes, $remainingSeconds);
        }
        return sprintf('%02dm %02dd', $minutes, $remainingSeconds);
    }
}
