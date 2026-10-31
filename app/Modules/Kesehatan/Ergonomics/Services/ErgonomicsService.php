<?php

namespace App\Modules\Kesehatan\Ergonomics\Services;

use App\Modules\Kesehatan\Ergonomics\Repositories\ErgonomicsRepository;
use App\Modules\Kesehatan\Ergonomics\Enums\ErgonomicsScoreCategory;

class ErgonomicsService
{
    public function __construct(
        protected ErgonomicsRepository $ergonomicsRepository
    ) {}

    public function getDashboardData(string $instansiId, array $filters = []): array
    {
        $query = $this->ergonomicsRepository->getLogsQuery($instansiId, $filters);

        $statQuery = (clone $query)->reorder();
        $statQuery->getQuery()->columns = null;

        $stats = $statQuery
            ->selectRaw("
                COUNT(*) as total_logs,
                COALESCE(AVG(ergonomics_logs.sedentary_minutes), 0) as avg_sedentary_mins,
                COALESCE(AVG(ergonomics_logs.score), 0) as avg_score,
                COUNT(CASE WHEN ergonomics_logs.posture_complaint IS NOT NULL AND ergonomics_logs.posture_complaint != '' THEN 1 END) as total_complaints,
                COUNT(CASE WHEN ergonomics_logs.score >= 75 THEN 1 END) as score_prima,
                COUNT(CASE WHEN ergonomics_logs.score >= 50 AND ergonomics_logs.score <= 74 THEN 1 END) as score_waspada,
                COUNT(CASE WHEN ergonomics_logs.score < 50 AND ergonomics_logs.score IS NOT NULL THEN 1 END) as score_berisiko
            ")
            ->first();

        $totalLogs = (int) ($stats->total_logs ?? 0);
        $avgSedentaryMins = round((float) ($stats->avg_sedentary_mins ?? 0));
        $avgScore = round((float) ($stats->avg_score ?? 0), 1);
        $totalComplaints = (int) ($stats->total_complaints ?? 0);

        $scorePrima = (int) ($stats->score_prima ?? 0);
        $scoreWaspada = (int) ($stats->score_waspada ?? 0);
        $scoreBerisiko = (int) ($stats->score_berisiko ?? 0);

        $ergonomicsScoreChartData = [
            'labels' => [
                ErgonomicsScoreCategory::PRIMA->label(),
                ErgonomicsScoreCategory::WASPADA->label(),
                ErgonomicsScoreCategory::BERISIKO->label(),
            ],
            'data' => [$scorePrima, $scoreWaspada, $scoreBerisiko],
            'colors' => [
                ErgonomicsScoreCategory::PRIMA->color(),
                ErgonomicsScoreCategory::WASPADA->color(),
                ErgonomicsScoreCategory::BERISIKO->color(),
            ],
        ];

        // Chart 2: Rata-rata Menit Sedentary per Divisi
        $divSedentaryStats = $this->ergonomicsRepository->getDivisionSedentaryStats($instansiId);
        $divisionSedentaryChartData = [
            'labels' => $divSedentaryStats->pluck('nama_divisi')->toArray(),
            'data' => $divSedentaryStats->pluck('avg_minutes')->toArray(),
        ];

        $logs = $this->ergonomicsRepository->getPaginatedLogs($query, 15);
        $divisions = $this->ergonomicsRepository->getDivisionsForInstansi($instansiId);

        return compact(
            'logs',
            'divisions',
            'totalLogs',
            'avgSedentaryMins',
            'avgScore',
            'totalComplaints',
            'ergonomicsScoreChartData',
            'divisionSedentaryChartData'
        );
    }
}
