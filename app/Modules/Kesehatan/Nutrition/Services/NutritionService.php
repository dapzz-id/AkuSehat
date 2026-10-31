<?php

namespace App\Modules\Kesehatan\Nutrition\Services;

use App\Modules\Kesehatan\Nutrition\Repositories\NutritionRepository;

class NutritionService
{
    public function __construct(
        protected NutritionRepository $nutritionRepository
    ) {}

    public function getDashboardData(string $instansiId, array $filters = []): array
    {
        $query = $this->nutritionRepository->getLogsQuery($instansiId, $filters);

        $statQuery = (clone $query)->reorder();
        $statQuery->getQuery()->columns = null;

        $stats = $statQuery
            ->selectRaw("
                COUNT(*) as total_logs,
                COALESCE(AVG(nutrition_logs.calories), 0) as avg_calories,
                COUNT(CASE WHEN nutrition_logs.is_balanced = true OR nutrition_logs.is_balanced = 1 THEN 1 END) as balanced_count
            ")
            ->first();

        $totalLogs = (int) ($stats->total_logs ?? 0);
        $avgCalories = round((float) ($stats->avg_calories ?? 0));
        $balancedCount = (int) ($stats->balanced_count ?? 0);
        $balancedRatio = $totalLogs > 0 ? round(($balancedCount / $totalLogs) * 100) : 0;

        $logs = $this->nutritionRepository->getPaginatedLogs($query, 15);
        $divisions = $this->nutritionRepository->getDivisionsForInstansi($instansiId);

        return compact(
            'logs',
            'divisions',
            'totalLogs',
            'avgCalories',
            'balancedRatio'
        );
    }
}
