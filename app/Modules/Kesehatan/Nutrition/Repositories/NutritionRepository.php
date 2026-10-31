<?php

namespace App\Modules\Kesehatan\Nutrition\Repositories;

use App\Models\NutritionLog;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NutritionRepository
{
    public function getLogsQuery(string $instansiId, array $filters = []): Builder
    {
        $query = NutritionLog::join('users', 'nutrition_logs.user_id', '=', 'users.id')
            ->where('users.instansi_id', $instansiId)
            ->select('nutrition_logs.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi');

        if (!empty($filters['divisi_id'])) {
            $query->where('users.id_divisi', $filters['divisi_id']);
        }

        if (!empty($filters['meal_type'])) {
            $query->where('nutrition_logs.meal_type', $filters['meal_type']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('nutrition_logs.food_name', 'like', "%{$s}%")
                  ->orWhere('users.nama', 'like', "%{$s}%");
            });
        }

        return $query;
    }

    public function getPaginatedLogs(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->orderBy('nutrition_logs.log_date', 'desc')->paginate($perPage)->withQueryString();
    }

    public function getDivisionsForInstansi(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }
}
