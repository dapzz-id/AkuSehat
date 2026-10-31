<?php

namespace App\Modules\Kesehatan\Ergonomics\Repositories;

use App\Models\ErgonomicsLog;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ErgonomicsRepository
{
    public function getLogsQuery(string $instansiId, array $filters = []): Builder
    {
        $query = ErgonomicsLog::join('users', 'ergonomics_logs.user_id', '=', 'users.id')
            ->leftJoin('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->select('ergonomics_logs.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi', 'divisi.divisi_name');

        if (!empty($filters['divisi_id'])) {
            $query->where('users.id_divisi', $filters['divisi_id']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where('users.nama', 'like', "%{$s}%");
        }

        return $query;
    }

    public function getPaginatedLogs(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->orderBy('ergonomics_logs.log_date', 'desc')->paginate($perPage)->withQueryString();
    }

    public function getDivisionsForInstansi(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }

    public function getDivisionSedentaryStats(string $instansiId, int $limit = 6): Collection
    {
        return Divisi::where('instansi_id', $instansiId)
            ->get()
            ->map(function ($div) {
                $avgMins = round(ErgonomicsLog::join('users', 'ergonomics_logs.user_id', '=', 'users.id')
                    ->where('users.id_divisi', $div->id)
                    ->avg('ergonomics_logs.sedentary_minutes') ?? 0);

                return [
                    'nama_divisi' => $div->divisi_name,
                    'avg_minutes' => $avgMins,
                ];
            })
            ->sortByDesc('avg_minutes')
            ->take($limit)
            ->values();
    }
}
