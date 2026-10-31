<?php

namespace App\Modules\Kesehatan\VitalLogs\Services;

use App\Modules\Kesehatan\VitalLogs\Repositories\VitalLogRepository;
use App\Models\User;
use App\Models\UserVitalLog;
use Carbon\Carbon;

class VitalLogService
{
    public function __construct(
        protected VitalLogRepository $vitalLogRepository
    ) {}

    public function getDashboardData(User $actor, array $filters, ?string $dateFilter = null): array
    {
        $instansiId = (string) $actor->instansi_id;
        $divisiId = $filters['divisi_id'] ?? null;
        $dateFilter = $dateFilter ?: Carbon::today()->toDateString();

        $stats = $this->vitalLogRepository->getDashboardAggregates($instansiId, $divisiId, $dateFilter);

        $totalLogs = $stats['total_logs'];
        $avgWater = $stats['avg_water'];
        $avgSleep = $stats['avg_sleep'];
        $avgMood = $stats['avg_mood'];
        $sleepKurang = $stats['sleep_kurang'];
        $sleepIdeal = $stats['sleep_ideal'];
        $sleepLebih = $stats['sleep_lebih'];

        $sleepChartData = [
            'labels' => ['Kurang (< 6 Jam)', 'Ideal (6 - 8 Jam)', 'Cukup/Panjang (> 8 Jam)'],
            'data' => [$sleepKurang, $sleepIdeal, $sleepLebih],
            'colors' => ['#f43f5e', '#10b981', '#6366f1']
        ];

        $divWaterStats = $this->vitalLogRepository->getDivisionWaterStats($instansiId, $dateFilter);
        $waterDivChartData = [
            'labels' => $divWaterStats->pluck('name')->toArray(),
            'data' => $divWaterStats->pluck('avg_water')->toArray(),
        ];
        $divisionWaterChartData = $waterDivChartData;

        $recentLogs = $this->vitalLogRepository->getPaginatedLogs($instansiId, $divisiId, $dateFilter, 15);
        $logs = $recentLogs;
        $divisiList = $this->vitalLogRepository->getInstansiDivisions($instansiId);
        $divisions = $divisiList;

        return compact(
            'totalLogs', 'avgWater', 'avgSleep', 'avgMood',
            'sleepChartData', 'waterDivChartData', 'divisionWaterChartData', 'recentLogs', 'logs',
            'divisiList', 'divisions', 'dateFilter'
        );
    }

    public function getToday(User $user): array
    {
        $today = Carbon::today()->toDateString();
        $log = $this->vitalLogRepository->getOrCreateTodayLog($user->id, $today);

        $waterTargetMl = 2000;
        $waterProgressPercent = min(100, round(($log->water_intake_ml / $waterTargetMl) * 100));
        $moodEnum = $log->mood_score ? \App\Modules\Kesehatan\VitalLogs\Enums\MoodScore::tryFrom($log->mood_score) : null;

        return [
            'data' => $log,
            'meta' => [
                'water_target_ml' => $waterTargetMl,
                'water_progress_percent' => $waterProgressPercent,
                'mood_label' => $moodEnum?->label(),
                'date_formatted' => Carbon::parse($today)->locale('id')->isoFormat('dddd, D MMMM Y'),
            ],
        ];
    }

    public function addWater(User $user, int $amount): UserVitalLog
    {
        $today = Carbon::today()->toDateString();
        return $this->vitalLogRepository->addWater($user->id, $today, $amount);
    }

    public function updateLog(User $user, array $validated): UserVitalLog
    {
        $today = Carbon::today()->toDateString();
        $log = $this->vitalLogRepository->getOrCreateTodayLog($user->id, $today);

        $filteredData = array_filter($validated, fn($val) => !is_null($val));
        $log->update($filteredData);

        return $log->fresh();
    }

    public function getHistory(User $user, int $days = 7)
    {
        return $this->vitalLogRepository->getHistory($user->id, $days);
    }
}
