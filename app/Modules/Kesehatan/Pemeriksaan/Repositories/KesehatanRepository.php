<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Repositories;

use App\Models\Kesehatan;
use App\Models\User;
use App\Models\Divisi;
use App\Modules\User\Enums\UserRole;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BmiStatus;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BloodPressureStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KesehatanRepository
{
    public function getQueryForInstansi(string $instansiId): Builder
    {
        return Kesehatan::with(['user', 'user.divisi'])
            ->whereHas('user', function ($query) use ($instansiId) {
                $query->where('level', UserRole::MEMBER->value)
                      ->whereNotNull('instansi_id')
                      ->where('instansi_id', $instansiId);
            });
    }

    public function findOrFail(string|int $id): Kesehatan
    {
        return Kesehatan::findOrFail($id);
    }

    public function create(array $data): Kesehatan
    {
        return Kesehatan::create($data);
    }

    public function update(Kesehatan $kesehatan, array $data): bool
    {
        return $kesehatan->update($data);
    }

    public function delete(Kesehatan $kesehatan): bool
    {
        return (bool) $kesehatan->delete();
    }

    public function massDelete(string $instansiId, array $ids): int
    {
        return Kesehatan::whereIn('id_kesehatan', $ids)
            ->where(function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId)
                  ->orWhereHas('user', function ($uq) use ($instansiId) {
                      $uq->where('instansi_id', $instansiId);
                  });
            })
            ->delete();
    }

    public function getPaginatedForDashboard(string $instansiId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->buildFilteredQuery($instansiId, $filters);

        return $query->whereNotNull('tgl')
            ->orderBy('tgl', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getDashboardAggregates(string $instansiId, array $filters): array
    {
        $query = $this->buildFilteredQuery($instansiId, $filters);
        $chartQuery = (clone $query)->reorder();
        $chartQuery->getQuery()->columns = null;

        // Compatible across PostgreSQL, MySQL, SQLite using standard CASE WHEN
        $agg = $chartQuery->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = '" . BmiStatus::NORMAL->value . "' THEN 1 END) as count_normal,
            COUNT(CASE WHEN status = '" . BmiStatus::KURUS->value . "' OR status = 'Underweight' THEN 1 END) as count_underweight,
            COUNT(CASE WHEN status = '" . BmiStatus::GEMUK->value . "' OR status = 'Overweight' THEN 1 END) as count_overweight,
            COUNT(CASE WHEN status LIKE '" . BmiStatus::OBESITAS->value . "%' THEN 1 END) as count_obesitas,
            COUNT(CASE WHEN status_darah = '" . BloodPressureStatus::HIPOTENSI->value . "' THEN 1 END) as count_hipotensi,
            COUNT(CASE WHEN status_darah = '" . BloodPressureStatus::NORMAL->value . "' THEN 1 END) as count_normal_darah,
            COUNT(CASE WHEN status_darah = 'Elevasi' OR status_darah = '" . BloodPressureStatus::PRAHIPERTENSI->value . "' THEN 1 END) as count_elevasi,
            COUNT(CASE WHEN status_darah LIKE 'Hipertensi%' THEN 1 END) as count_hipertensi,
            COUNT(CASE WHEN status_darah = 'Krisis' THEN 1 END) as count_krisis
        ")->first();

        return [
            'total' => (int) ($agg->total ?? 0),
            'count_normal' => (int) ($agg->count_normal ?? 0),
            'count_underweight' => (int) ($agg->count_underweight ?? 0),
            'count_overweight' => (int) ($agg->count_overweight ?? 0),
            'count_obesitas' => (int) ($agg->count_obesitas ?? 0),
            'count_hipotensi' => (int) ($agg->count_hipotensi ?? 0),
            'count_normal_darah' => (int) ($agg->count_normal_darah ?? 0),
            'count_elevasi' => (int) ($agg->count_elevasi ?? 0),
            'count_hipertensi' => (int) ($agg->count_hipertensi ?? 0),
            'count_krisis' => (int) ($agg->count_krisis ?? 0),
        ];
    }

    public function getDistinctYears(string $instansiId): array
    {
        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'pgsql' ? 'EXTRACT(YEAR FROM tgl)' : 'YEAR(tgl)';

        $years = Kesehatan::whereHas('user', function ($query) use ($instansiId) {
                $query->where('level', UserRole::MEMBER->value)
                    ->whereNotNull('instansi_id')
                    ->where('instansi_id', $instansiId);
            })
            ->selectRaw("{$yearExpression} as year")
            ->whereNotNull('tgl')
            ->groupBy('year')
            ->pluck('year')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->toArray();

        $currentYear = (int) now()->year;
        if (!in_array($currentYear, $years)) {
            $years[] = $currentYear;
        }

        rsort($years);
        return $years;
    }

    public function getInstansiDivisions(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name', 'asc')->get();
    }

    public function getInstansiMembersForForm(string $instansiId): Collection
    {
        return User::with('divisi')
            ->select('users.*')
            ->leftJoin('divisi', 'divisi.id', '=', 'users.id_divisi')
            ->where('users.level', UserRole::MEMBER->value)
            ->whereNotNull('users.instansi_id')
            ->whereNotNull('users.id_divisi')
            ->where('users.instansi_id', $instansiId)
            ->orderBy('divisi.divisi_name', 'asc')
            ->orderBy('users.nama', 'asc')
            ->get();
    }

    private function buildFilteredQuery(string $instansiId, array $filters): Builder
    {
        $query = Kesehatan::with(['user', 'user.divisi'])
            ->whereHas('user', function ($query) use ($instansiId) {
                $query->where('level', UserRole::MEMBER->value)
                      ->whereNotNull('instansi_id')
                      ->where('instansi_id', $instansiId);
            });

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%")
                      ->orWhere('nomor_induk', 'like', "%{$search}%")
                      ->orWhereHas('divisi', function ($d) use ($search) {
                          $d->where('divisi_name', 'like', "%{$search}%");
                      });
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('status_darah', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['year'])) {
            if ($filters['year'] !== 'all') {
                $query->whereYear('tgl', $filters['year']);
            }
        } else {
            $query->whereYear('tgl', Carbon::now()->year);
        }

        return $query;
    }

    public function getByUserGrouped(int|string $userId)
    {
        $kesehatan = Kesehatan::where('id_user', $userId)
            ->orderBy('tgl', 'desc')
            ->get();

        $grouped = $kesehatan->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tgl)->format('m');
            })->map(function ($monthGroup, $month) {
                $monthName = \Carbon\Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
                return [
                    'month' => strtolower($monthName),
                    'data' => $monthGroup->values(),
                ];
            })->values();
        });

        return $grouped->map(function ($months, $year) {
            return [
                'year' => (int) $year,
                'months' => $months,
            ];
        })->values();
    }

    public function getExportRecords(string $instansiId, array $filters): Collection
    {
        $filterType = $filters['filter_type'] ?? 'all';

        $query = Kesehatan::with(['user', 'user.divisi'])
            ->select('kesehatan.*')
            ->join('users', 'kesehatan.id_user', '=', 'users.id')
            ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.level', UserRole::MEMBER->value)
            ->whereNotNull('users.instansi_id')
            ->where('users.instansi_id', $instansiId)
            ->whereNotNull('kesehatan.tgl')
            ->orderBy('divisi.divisi_name', 'asc')
            ->orderBy('users.nama', 'asc');

        if (in_array($filterType, ['divisi', 'divisi_tanggal']) && !empty($filters['divisi'])) {
            $query->where('users.id_divisi', $filters['divisi']);
        }

        if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
            if (!empty($filters['from'])) {
                $query->whereDate('kesehatan.tgl', '>=', $filters['from']);
            }
            if (!empty($filters['to'])) {
                $query->whereDate('kesehatan.tgl', '<=', $filters['to']);
            }
            if (empty($filters['from']) && empty($filters['to'])) {
                $query->whereYear('kesehatan.tgl', now()->year);
            }
        }

        if (in_array($filterType, ['all', 'divisi'])) {
            $query->whereYear('kesehatan.tgl', now()->year);
        }

        return $query->orderBy('kesehatan.tgl', 'desc')->get();
    }
}
