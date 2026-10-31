<?php

namespace App\Modules\Hb\PemeriksaanHb\Repositories;

use App\Models\Hb;
use App\Models\User;
use App\Models\Divisi;
use App\Modules\Hb\PemeriksaanHb\Enums\HbStatus;
use App\Modules\User\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HbRepository
{
    public function getQueryForInstansi(string $instansiId): Builder
    {
        return Hb::with(['user', 'user.divisi'])
            ->whereHas('user', function ($query) use ($instansiId) {
                $query->where('level', UserRole::MEMBER->value)
                      ->whereNotNull('instansi_id')
                      ->where('instansi_id', $instansiId);
            });
    }

    public function findOrFail(string|int $id): Hb
    {
        return Hb::findOrFail($id);
    }

    public function create(array $data): Hb
    {
        return Hb::create($data);
    }

    public function update(Hb $hb, array $data): bool
    {
        return $hb->update($data);
    }

    public function delete(Hb $hb): bool
    {
        return (bool) $hb->delete();
    }

    public function massDelete(string $instansiId, array $ids): int
    {
        return Hb::whereIn('id_hb', $ids)
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

    public function getStatusAggregates(string $instansiId, array $filters): array
    {
        $query = $this->buildFilteredQuery($instansiId, $filters);
        $hbQuery = (clone $query)->reorder();
        $hbQuery->getQuery()->columns = null;

        // Compatible with PostgreSQL, MySQL, and SQLite using standard CASE WHEN
        $agg = $hbQuery->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = '" . HbStatus::NORMAL->value . "' THEN 1 END) as count_normal,
            COUNT(CASE WHEN status = '" . HbStatus::ANEMIA->value . "' THEN 1 END) as count_anemia,
            COUNT(CASE WHEN status = '" . HbStatus::TINGGI->value . "' THEN 1 END) as count_tinggi
        ")->first();

        return [
            'total' => (int) ($agg->total ?? 0),
            'count_normal' => (int) ($agg->count_normal ?? 0),
            'count_anemia' => (int) ($agg->count_anemia ?? 0),
            'count_tinggi' => (int) ($agg->count_tinggi ?? 0),
        ];
    }

    public function getDivisionAverages(string $instansiId, int $limit = 6): array
    {
        $divisionHb = Hb::join('users', 'hb.id_user', '=', 'users.id')
            ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->where('users.level', UserRole::MEMBER->value)
            ->whereNotNull('hb.tgl')
            ->select('divisi.divisi_name', DB::raw('ROUND(AVG(CAST(hb.hb AS DECIMAL(8,2))), 1) as avg_hb'))
            ->groupBy('divisi.divisi_name')
            ->orderBy('avg_hb', 'asc')
            ->limit($limit)
            ->get();

        return [
            'labels' => $divisionHb->pluck('divisi_name')->toArray(),
            'data' => $divisionHb->pluck('avg_hb')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    public function getDistinctYears(string $instansiId): array
    {
        $driver = DB::connection()->getDriverName();
        $yearExpression = $driver === 'pgsql' ? 'EXTRACT(YEAR FROM tgl)' : 'YEAR(tgl)';

        $years = Hb::whereHas('user', function ($query) use ($instansiId) {
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
        $query = Hb::with(['user', 'user.divisi'])
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
                ->orWhere('hb', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
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
        $hb = Hb::where('id_user', $userId)
            ->orderBy('tgl', 'desc')
            ->get();

        $grouped = $hb->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tgl)->format('m');
            })->map(function ($monthGroup, $month) {
                $monthName = \Carbon\Carbon::createFromFormat('m', $month)
                    ->locale('id')
                    ->translatedFormat('F');
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

    public function getExportRecords(string $instansiId, array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $filterType = $filters['filter_type'] ?? 'all';

        $query = Hb::with(['user', 'user.divisi'])
            ->select('hb.*')
            ->join('users', 'hb.id_user', '=', 'users.id')
            ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.level', UserRole::MEMBER->value)
            ->whereNotNull('users.instansi_id')
            ->where('users.instansi_id', $instansiId)
            ->whereNotNull('hb.tgl')
            ->orderBy('divisi.divisi_name', 'asc')
            ->orderBy('users.nama', 'asc');

        if (in_array($filterType, ['divisi', 'divisi_tanggal']) && !empty($filters['divisi'])) {
            $query->where('users.id_divisi', $filters['divisi']);
        }

        if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
            if (!empty($filters['from'])) {
                $query->whereDate('hb.tgl', '>=', $filters['from']);
            }
            if (!empty($filters['to'])) {
                $query->whereDate('hb.tgl', '<=', $filters['to']);
            }
            if (empty($filters['from']) && empty($filters['to'])) {
                $query->whereYear('hb.tgl', now()->year);
            }
        }

        if (in_array($filterType, ['all', 'divisi'])) {
            $query->whereYear('hb.tgl', now()->year);
        }

        return $query->orderBy('hb.tgl', 'desc')->get();
    }
}
