<?php

namespace App\Modules\Kesehatan\Activities\Repositories;

use App\Models\UserActivity;
use App\Models\Divisi;
use App\Modules\Kesehatan\Activities\Enums\ActivityType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityRepository
{
    public function getActivitiesForAdmin(int|string $instansiId, ?string $divisiId = null, ?string $activityType = null)
    {
        $query = UserActivity::join('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->select('user_activities.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi', 'divisi.divisi_name');

        if (!empty($divisiId)) {
            $query->where('users.id_divisi', $divisiId);
        }

        if (!empty($activityType)) {
            $query->where('user_activities.activity_type', $activityType);
        }

        return $query;
    }

    public function getDashboardAggregates(int|string $instansiId, ?string $divisiId = null, ?string $activityType = null): array
    {
        $runType = ActivityType::RUN->value;
        $walkType = ActivityType::WALK->value;
        $cyclingType = ActivityType::CYCLING->value;

        $query = $this->getActivitiesForAdmin($instansiId, $divisiId, $activityType);
        $statQuery = (clone $query)->reorder();
        $statQuery->getQuery()->columns = null;

        $stats = $statQuery->selectRaw("
            COALESCE(SUM(user_activities.distance_meters), 0) as total_distance_meters,
            COUNT(*) as total_sessions,
            COALESCE(SUM(user_activities.calories), 0) as total_calories,
            COALESCE(SUM(user_activities.steps), 0) as total_steps,
            COUNT(CASE WHEN user_activities.activity_type = '{$runType}' THEN 1 END) as count_run,
            COUNT(CASE WHEN user_activities.activity_type = '{$walkType}' THEN 1 END) as count_walk,
            COUNT(CASE WHEN user_activities.activity_type = '{$cyclingType}' THEN 1 END) as count_cycling,
            COUNT(CASE WHEN user_activities.activity_type NOT IN ('{$runType}', '{$walkType}', '{$cyclingType}') THEN 1 END) as count_other
        ")->first();

        return [
            'total_distance_meters' => (float) ($stats->total_distance_meters ?? 0),
            'total_sessions' => (int) ($stats->total_sessions ?? 0),
            'total_calories' => (float) ($stats->total_calories ?? 0),
            'total_steps' => (int) ($stats->total_steps ?? 0),
            'count_run' => (int) ($stats->count_run ?? 0),
            'count_walk' => (int) ($stats->count_walk ?? 0),
            'count_cycling' => (int) ($stats->count_cycling ?? 0),
            'count_other' => (int) ($stats->count_other ?? 0),
        ];
    }

    public function getPaginatedActivities(int|string $instansiId, ?string $divisiId = null, ?string $activityType = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getActivitiesForAdmin($instansiId, $divisiId, $activityType)
            ->orderBy('user_activities.started_at', 'desc')
            ->paginate($perPage);
    }

    public function getInstansiDivisions(int|string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }

    public function getDivisionStats(int|string $instansiId)
    {
        return Divisi::where('instansi_id', $instansiId)
            ->withCount('users')
            ->get()
            ->map(function ($div) {
                $divActs = UserActivity::join('users', 'user_activities.user_id', '=', 'users.id')
                    ->where('users.id_divisi', $div->id);
                $divDist = (clone $divActs)->sum('user_activities.distance_meters');
                $divSessions = (clone $divActs)->count();

                return [
                    'id' => $div->id,
                    'name' => $div->divisi_name,
                    'nama_divisi' => $div->divisi_name,
                    'total_distance_km' => round($divDist / 1000, 2),
                    'total_km' => round($divDist / 1000, 1),
                    'total_members' => $div->users_count,
                    'user_count' => $div->users_count,
                    'total_sessions' => $divSessions,
                ];
            })
            ->sortByDesc('total_distance_km')
            ->values();
    }

    public function getUserActivities(int|string $userId, ?string $type = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = UserActivity::where('user_id', $userId);
        if (!empty($type)) {
            $query->where('activity_type', $type);
        }
        return $query->orderBy('started_at', 'desc')->paginate($perPage);
    }

    public function getUserSummary(int|string $userId, ?string $type = null): array
    {
        $query = UserActivity::where('user_id', $userId);
        if (!empty($type)) {
            $query->where('activity_type', $type);
        }

        return [
            'total_activities' => (clone $query)->count(),
            'total_distance_meters' => (clone $query)->sum('distance_meters'),
            'total_duration_seconds' => (int) (clone $query)->sum('duration_seconds'),
            'total_calories' => round((clone $query)->sum('calories'), 1),
            'total_steps' => (int) (clone $query)->sum('steps'),
        ];
    }

    public function create(array $data): UserActivity
    {
        return UserActivity::create($data);
    }

    public function findUserActivity(int|string $userId, int|string $id): ?UserActivity
    {
        return UserActivity::where('user_id', $userId)->find($id);
    }

    public function deleteUserActivity(int|string $userId, int|string $id): bool
    {
        $activity = $this->findUserActivity($userId, $id);
        if (!$activity) {
            return false;
        }
        return (bool) $activity->delete();
    }

    public function getLeaderboard(?string $instansiId, string $period = 'month', int $limit = 20, int $offset = 0)
    {
        $query = UserActivity::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->join('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->leftJoin('instansi', 'users.instansi_id', '=', 'instansi.id')
            ->selectRaw('users.id, users.nama, users.avatar, divisi.divisi_name, instansi.nama_instansi,
                SUM(user_activities.distance_meters) as total_distance_meters, 
                SUM(user_activities.duration_seconds) as total_duration_seconds,
                SUM(user_activities.calories) as total_calories,
                COUNT(user_activities.id) as total_sessions,
                MIN(CASE WHEN user_activities.distance_meters >= 200 AND user_activities.duration_seconds > 30 
                    THEN (user_activities.duration_seconds / (user_activities.distance_meters / 1000.0)) 
                    ELSE NULL END) as best_pace_seconds')
            ->where('user_activities.distance_meters', '>', 0)
            ->where('user_activities.duration_seconds', '>', 0)
            ->groupBy('users.id', 'users.nama', 'users.avatar', 'divisi.divisi_name', 'instansi.nama_instansi');

        // Security: Prevent cross-institution data leakage in local scope
        if ($instansiId === 'NO_INSTANSI') {
            $query->whereRaw('1 = 0');
        } elseif ($instansiId !== null) {
            $query->where('users.instansi_id', $instansiId);
        }

        // Strict date boundaries to completely prevent data leakage across periods / years
        $now = now();
        if ($period === 'week') {
            $query->whereBetween('user_activities.started_at', [
                $now->copy()->startOfWeek()->toDateTimeString(),
                $now->copy()->endOfWeek()->toDateTimeString()
            ]);
        } elseif ($period === 'year' || $period === 'this_year') {
            // Strictly this year: 2026-01-01 00:00:00 to 2026-12-31 23:59:59 (no previous year data)
            $query->whereBetween('user_activities.started_at', [
                $now->copy()->startOfYear()->toDateTimeString(),
                $now->copy()->endOfYear()->toDateTimeString()
            ]);
        } elseif ($period === 'last_year') {
            // Strictly last year: 2025-01-01 00:00:00 to 2025-12-31 23:59:59
            $query->whereBetween('user_activities.started_at', [
                $now->copy()->subYear()->startOfYear()->toDateTimeString(),
                $now->copy()->subYear()->endOfYear()->toDateTimeString()
            ]);
        } else {
            // Strictly this month: e.g. 2026-10-01 00:00:00 to 2026-10-31 23:59:59 (no previous month / last year)
            $query->whereBetween('user_activities.started_at', [
                $now->copy()->startOfMonth()->toDateTimeString(),
                $now->copy()->endOfMonth()->toDateTimeString()
            ]);
        }

        $query->orderByDesc('total_distance_meters');

        if ($offset > 0) {
            $query->skip($offset);
        }

        return $query->take($limit)->get();
    }
}
