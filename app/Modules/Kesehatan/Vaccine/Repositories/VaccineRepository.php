<?php

namespace App\Modules\Kesehatan\Vaccine\Repositories;

use App\Models\VaccineRecord;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VaccineRepository
{
    public function getRecordsQuery(string $instansiId, array $filters = []): Builder
    {
        $query = VaccineRecord::join('users', 'vaccine_records.user_id', '=', 'users.id')
            ->where('users.instansi_id', $instansiId)
            ->select('vaccine_records.*', 'users.nama as user_nama', 'users.nomor_induk', 'users.id_divisi');

        if (!empty($filters['divisi_id'])) {
            $query->where('users.id_divisi', $filters['divisi_id']);
        }

        if (!empty($filters['vaccine_name'])) {
            $query->where('vaccine_records.vaccine_name', 'like', "%" . $filters['vaccine_name'] . "%");
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('vaccine_records.vaccine_name', 'like', "%{$s}%")
                  ->orWhere('users.nama', 'like', "%{$s}%");
            });
        }

        return $query;
    }

    public function getPaginatedRecords(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->orderBy('vaccine_records.administered_date', 'desc')->paginate($perPage)->withQueryString();
    }

    public function getDivisionsForInstansi(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }
}
