<?php

namespace App\Modules\Kesehatan\Burnout\Repositories;

use App\Models\BurnoutScreening;
use App\Models\Divisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BurnoutRepository
{
    public function getScreeningQuery(string $instansiId, array $filters = []): Builder
    {
        $query = BurnoutScreening::join('users', 'burnout_screenings.user_id', '=', 'users.id')
            ->where('users.instansi_id', $instansiId)
            ->select('burnout_screenings.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi');

        if (!empty($filters['divisi_id'])) {
            $query->where('users.id_divisi', $filters['divisi_id']);
        }

        if (!empty($filters['burnout_level'])) {
            $query->where('burnout_screenings.burnout_level', $filters['burnout_level']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where('users.nama', 'like', "%{$s}%");
        }

        return $query;
    }

    public function getPaginatedScreenings(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->orderBy('burnout_screenings.screening_date', 'desc')->paginate($perPage)->withQueryString();
    }

    public function getDivisionScores(string $instansiId, int $limit = 6): Collection
    {
        return BurnoutScreening::join('users', 'burnout_screenings.user_id', '=', 'users.id')
            ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->select('divisi.divisi_name', DB::raw('round(avg(burnout_screenings.stress_score), 1) as avg_score'))
            ->groupBy('divisi.divisi_name')
            ->orderByDesc('avg_score')
            ->limit($limit)
            ->get();
    }

    public function getDivisionsForInstansi(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }
}
