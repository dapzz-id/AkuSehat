<?php

namespace App\Modules\Kesehatan\Burnout\Services;

use App\Modules\Kesehatan\Burnout\Repositories\BurnoutRepository;
use App\Modules\Kesehatan\Burnout\Enums\BurnoutLevel;

class BurnoutService
{
    public function __construct(
        protected BurnoutRepository $burnoutRepository
    ) {}

    public function getDashboardData(string $instansiId, array $filters = []): array
    {
        $query = $this->burnoutRepository->getScreeningQuery($instansiId, $filters);

        $statQuery = (clone $query)->reorder();
        $statQuery->getQuery()->columns = null;

        $highVal = BurnoutLevel::TINGGI->value;
        $modVal = BurnoutLevel::SEDANG->value;
        $lowVal = BurnoutLevel::RENDAH->value;

        $stats = $statQuery
            ->selectRaw("
                COUNT(*) as total_screenings,
                COUNT(CASE WHEN burnout_screenings.burnout_level = '{$highVal}' THEN 1 END) as high_risk_count,
                COUNT(CASE WHEN burnout_screenings.burnout_level = '{$modVal}' THEN 1 END) as moderate_count,
                COUNT(CASE WHEN burnout_screenings.burnout_level = '{$lowVal}' THEN 1 END) as low_risk_count,
                COALESCE(AVG(burnout_screenings.stress_score), 0) as avg_stress_score
            ")
            ->first();

        $totalScreenings = (int) ($stats->total_screenings ?? 0);
        $highRiskCount = (int) ($stats->high_risk_count ?? 0);
        $moderateCount = (int) ($stats->moderate_count ?? 0);
        $lowRiskCount = (int) ($stats->low_risk_count ?? 0);
        $avgStressScore = round((float) ($stats->avg_stress_score ?? 0), 1);

        $burnoutChartData = [
            'labels' => [
                BurnoutLevel::RENDAH->label(),
                BurnoutLevel::SEDANG->label(),
                BurnoutLevel::TINGGI->label(),
            ],
            'data' => [$lowRiskCount, $moderateCount, $highRiskCount],
            'colors' => [
                BurnoutLevel::RENDAH->color(),
                BurnoutLevel::SEDANG->color(),
                BurnoutLevel::TINGGI->color(),
            ],
        ];

        $divisionScores = $this->burnoutRepository->getDivisionScores($instansiId);
        $divisionChartData = [
            'labels' => $divisionScores->pluck('divisi_name')->toArray(),
            'data' => $divisionScores->pluck('avg_score')->toArray(),
        ];

        $screenings = $this->burnoutRepository->getPaginatedScreenings($query, 15);
        $divisions = $this->burnoutRepository->getDivisionsForInstansi($instansiId);

        return compact(
            'screenings',
            'divisions',
            'totalScreenings',
            'highRiskCount',
            'moderateCount',
            'lowRiskCount',
            'avgStressScore',
            'burnoutChartData',
            'divisionChartData'
        );
    }
}
