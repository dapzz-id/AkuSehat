<?php

namespace App\Modules\Kesehatan\Vaccine\Services;

use App\Modules\Kesehatan\Vaccine\Repositories\VaccineRepository;

class VaccineService
{
    public function __construct(
        protected VaccineRepository $vaccineRepository
    ) {}

    public function getDashboardData(string $instansiId, array $filters = []): array
    {
        $query = $this->vaccineRepository->getRecordsQuery($instansiId, $filters);

        $totalVaccinations = (clone $query)->count();
        $uniqueUsersVaccinated = (clone $query)->distinct('vaccine_records.user_id')->count('vaccine_records.user_id');

        $records = $this->vaccineRepository->getPaginatedRecords($query, 15);
        $divisions = $this->vaccineRepository->getDivisionsForInstansi($instansiId);

        return compact(
            'records',
            'divisions',
            'totalVaccinations',
            'uniqueUsersVaccinated'
        );
    }
}
