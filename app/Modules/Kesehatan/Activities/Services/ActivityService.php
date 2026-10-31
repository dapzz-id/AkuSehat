<?php

namespace App\Modules\Kesehatan\Activities\Services;

use App\Modules\Kesehatan\Activities\Repositories\ActivityRepository;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Cache;

class ActivityService
{
    public function __construct(
        protected ActivityRepository $activityRepository
    ) {}

    public function getDashboardData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;
        $divisiId = $filters['divisi_id'] ?? null;
        $activityType = $filters['activity_type'] ?? null;

        $stats = $this->activityRepository->getDashboardAggregates($instansiId, $divisiId, $activityType);

        $totalDistanceMeters = $stats['total_distance_meters'];
        $totalSessions = $stats['total_sessions'];
        $totalCalories = $stats['total_calories'];
        $totalSteps = $stats['total_steps'];
        $countRun = $stats['count_run'];
        $countWalk = $stats['count_walk'];
        $countCycling = $stats['count_cycling'];
        $countOther = $stats['count_other'];

        $activityTypeChartData = [
            'labels' => ['Lari (Run)', 'Jalan Santai (Walk)', 'Bersepeda (Cycling)', 'Lainnya'],
            'data' => [$countRun, $countWalk, $countCycling, $countOther],
            'colors' => ['#10b981', '#38bdf8', '#f59e0b', '#a855f7']
        ];

        $divisionStats = $this->activityRepository->getDivisionStats($instansiId);
        $divisionKmChartData = [
            'labels' => collect($divisionStats)->pluck('nama_divisi')->toArray(),
            'data' => collect($divisionStats)->pluck('total_km')->toArray(),
        ];

        $recentActivities = $this->activityRepository->getPaginatedActivities($instansiId, $divisiId, $activityType, 15);
        $divisiList = $this->activityRepository->getInstansiDivisions($instansiId);
        $divisions = $divisiList;
        $activities = $recentActivities;

        return compact(
            'totalDistanceMeters', 'totalSessions', 'totalCalories', 'totalSteps',
            'activityTypeChartData', 'divisionStats', 'divisionKmChartData', 'recentActivities', 'activities', 'divisiList', 'divisions'
        );
    }

    public function recordActivity(User $user, array $validated): UserActivity
    {
        $distance = (float) ($validated['distance_meters'] ?? 0);
        $duration = (int) ($validated['duration_seconds'] ?? 0);
        $speedKmh = $duration > 0 ? round(($distance / 1000) / ($duration / 3600), 2) : 0;

        $calories = (float) ($validated['calories'] ?? 0);
        if ($calories <= 0) {
            $activityType = \App\Modules\Kesehatan\Activities\Enums\ActivityType::tryFrom($validated['activity_type']);
            $met = $activityType ? $activityType->metValue() : 5.0;
            $weight = (float) ($user->kesehatan()->latest('tgl')->value('bb') ?? 60);
            $calories = round($met * $weight * ($duration / 3600), 1);
        }

        $activity = $this->activityRepository->create([
            'user_id' => $user->id,
            'activity_type' => $validated['activity_type'],
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'duration_seconds' => $duration,
            'distance_meters' => $distance,
            'calories' => $calories,
            'steps' => $validated['steps'] ?? 0,
            'avg_speed_kmh' => $speedKmh,
            'route_coordinates' => $validated['route_coordinates'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Invalidate leaderboard cache instantaneously on new activity
        try {
            Cache::increment('leaderboard_version');
        } catch (\Throwable $e) {}

        return $activity;
    }

    public function getActivityDetail(User $user, int|string $id): ?UserActivity
    {
        return $this->activityRepository->findUserActivity($user->id, $id);
    }

    public function deleteActivity(User $user, int|string $id): bool
    {
        $deleted = $this->activityRepository->deleteUserActivity($user->id, $id);
        if ($deleted) {
            try {
                Cache::increment('leaderboard_version');
            } catch (\Throwable $e) {}
        }
        return $deleted;
    }

    public function getLeaderboard(?string $instansiId, string $period = 'month', int $limit = 20, int $offset = 0, ?string $currentUserId = null): array
    {
        $version = Cache::get('leaderboard_version', 1);
        $safeInstansi = $instansiId ?? 'global';
        $cacheKey = "lb_v{$version}_{$safeInstansi}_{$period}_{$limit}_{$offset}";

        $raw = Cache::remember($cacheKey, 60, function () use ($instansiId, $period, $limit, $offset) {
            return $this->activityRepository->getLeaderboard($instansiId, $period, $limit, $offset);
        });

        return $raw->map(function ($item, $index) use ($currentUserId, $offset) {
            $totalSec = (int) $item->total_duration_seconds;
            $hours = floor($totalSec / 3600);
            $minutes = floor(($totalSec % 3600) / 60);
            $seconds = $totalSec % 60;
            $formattedDuration = $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m {$seconds}d";

            $totalDistKm = $item->total_distance_meters > 0 ? ($item->total_distance_meters / 1000.0) : 0;
            $overallPaceSec = $totalDistKm > 0 ? round($totalSec / $totalDistKm) : 0;
            $bestPaceSec = $item->best_pace_seconds ? round((float) $item->best_pace_seconds) : $overallPaceSec;

            return [
                'rank' => $offset + $index + 1,
                'user_id' => $item->id,
                'name' => $item->nama,
                'avatar' => $item->avatar ? (str_starts_with($item->avatar, 'http') ? $item->avatar : asset('storage/' . $item->avatar)) : null,
                'divisi' => $item->divisi_name ?: 'Umum',
                'instansi' => $item->nama_instansi ?: 'Instansi',
                'total_distance_km' => round($item->total_distance_meters / 1000, 2),
                'total_duration_seconds' => $totalSec,
                'formatted_duration' => $formattedDuration,
                'best_pace_seconds' => $bestPaceSec,
                'avg_pace_seconds' => $overallPaceSec,
                'total_calories' => (int) $item->total_calories,
                'total_sessions' => (int) $item->total_sessions,
                'is_current_user' => $currentUserId ? ($item->id === $currentUserId) : false,
            ];
        })->toArray();
    }
}
