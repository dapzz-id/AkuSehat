<?php

namespace App\Modules\Kesehatan\VitalLogs\Repositories;

use App\Models\UserVitalLog;
use App\Models\Divisi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VitalLogRepository
{
    public function getLogsForAdmin(int|string $instansiId, ?string $divisiId = null, ?string $dateFilter = null)
    {
        $query = UserVitalLog::join('users', 'user_vital_logs.user_id', '=', 'users.id')
            ->leftJoin('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->select('user_vital_logs.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi', 'divisi.divisi_name');

        if (!empty($divisiId)) {
            $query->where('users.id_divisi', $divisiId);
        }

        if (!empty($dateFilter)) {
            $query->where('user_vital_logs.log_date', $dateFilter);
        }

        return $query;
    }

    public function getDashboardAggregates(int|string $instansiId, ?string $divisiId = null, ?string $dateFilter = null): array
    {
        $query = $this->getLogsForAdmin($instansiId, $divisiId, $dateFilter);
        $statQuery = (clone $query)->reorder();
        $statQuery->getQuery()->columns = null;

        $stats = $statQuery->selectRaw("
            COUNT(*) as total_logs,
            COALESCE(ROUND(AVG(user_vital_logs.water_intake_ml)), 0) as avg_water,
            COALESCE(ROUND(AVG(user_vital_logs.sleep_hours), 1), 0) as avg_sleep,
            COALESCE(ROUND(AVG(user_vital_logs.mood_score), 1), 0) as avg_mood,
            COUNT(CASE WHEN user_vital_logs.sleep_hours < 6 THEN 1 END) as sleep_kurang,
            COUNT(CASE WHEN user_vital_logs.sleep_hours >= 6 AND user_vital_logs.sleep_hours <= 8 THEN 1 END) as sleep_ideal,
            COUNT(CASE WHEN user_vital_logs.sleep_hours > 8 THEN 1 END) as sleep_lebih
        ")->first();

        return [
            'total_logs' => (int) ($stats->total_logs ?? 0),
            'avg_water' => (float) ($stats->avg_water ?? 0),
            'avg_sleep' => (float) ($stats->avg_sleep ?? 0),
            'avg_mood' => (float) ($stats->avg_mood ?? 0),
            'sleep_kurang' => (int) ($stats->sleep_kurang ?? 0),
            'sleep_ideal' => (int) ($stats->sleep_ideal ?? 0),
            'sleep_lebih' => (int) ($stats->sleep_lebih ?? 0),
        ];
    }

    public function getPaginatedLogs(int|string $instansiId, ?string $divisiId = null, ?string $dateFilter = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getLogsForAdmin($instansiId, $divisiId, $dateFilter)
            ->orderBy('user_vital_logs.updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getInstansiDivisions(int|string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }

    public function getDivisionWaterStats(int|string $instansiId, string $dateFilter)
    {
        return Divisi::where('instansi_id', $instansiId)
            ->get()
            ->map(function ($div) use ($dateFilter) {
                $q = UserVitalLog::join('users', 'user_vital_logs.user_id', '=', 'users.id')
                    ->where('users.id_divisi', $div->id);
                if ($dateFilter) {
                    $q->where('user_vital_logs.log_date', $dateFilter);
                }
                $avg = $q->avg('user_vital_logs.water_intake_ml') ?? 0;

                return [
                    'name' => $div->divisi_name,
                    'avg_water' => round($avg),
                ];
            });
    }

    public function getOrCreateTodayLog(int|string $userId, string $date): UserVitalLog
    {
        return UserVitalLog::firstOrCreate(
            ['user_id' => $userId, 'log_date' => $date],
            [
                'water_intake_ml' => 0,
                'sleep_hours' => null,
                'mood_score' => null,
                'systolic' => null,
                'diastolic' => null,
                'blood_sugar' => null,
                'weight_kg' => null,
            ]
        );
    }

    public function addWater(int|string $userId, string $date, int $amount): UserVitalLog
    {
        $log = $this->getOrCreateTodayLog($userId, $date);
        $newAmount = max(0, (int) $log->water_intake_ml + $amount);
        $log->update(['water_intake_ml' => $newAmount]);
        return $log->fresh();
    }

    public function getHistory(int|string $userId, int $days = 7): Collection
    {
        return UserVitalLog::where('user_id', $userId)
            ->where('log_date', '>=', Carbon::today()->subDays($days)->toDateString())
            ->orderBy('log_date', 'asc')
            ->get();
    }
}
