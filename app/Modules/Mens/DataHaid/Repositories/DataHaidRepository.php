<?php

namespace App\Modules\Mens\DataHaid\Repositories;

use App\Models\DataHaid;
use App\Models\PeminjamanPita;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Modules\Mens\DataHaid\Enums\PitaStatus;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserGender;

class DataHaidRepository
{
    public function getActiveHaidForInstansi(string|int $instansiId): Collection
    {
        return DataHaid::with(['user.divisi'])
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->where('status', HaidStatus::BERLANGSUNG->value)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();
    }

    public function getHaidBetweenDates(string|int $instansiId, string $startDate, string $endDate): Collection
    {
        return DataHaid::with('user')
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('tanggal_mulai', '<=', $endDate)
                          ->where(function ($s2) use ($startDate) {
                              $s2->whereNull('tanggal_selesai')
                                 ->orWhere('tanggal_selesai', '>=', $startDate);
                          });
                  });
            })
            ->get();
    }

    public function getMonthHaidRecords(string|int $instansiId, string $monthStart, string $monthEnd): Collection
    {
        return DataHaid::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })
        ->with(['user', 'user.divisi'])
        ->where(function ($query) use ($monthStart, $monthEnd) {
            $query->whereBetween('tanggal_mulai', [$monthStart, $monthEnd])
                  ->orWhereBetween('tanggal_selesai', [$monthStart, $monthEnd])
                  ->orWhere(function ($sub) use ($monthStart, $monthEnd) {
                      $sub->where('tanggal_mulai', '<=', $monthStart)
                          ->where('tanggal_selesai', '>=', $monthEnd);
                  });
        })
        ->get();
    }

    public function getFemaleMembers(string|int $instansiId): Collection
    {
        return User::with('divisi')
            ->where('instansi_id', $instansiId)
            ->where('jk', UserGender::PEREMPUAN->value)
            ->where('level', UserRole::MEMBER->value)
            ->orderBy('nama')
            ->get();
    }

    public function getPitaHistoryPaginated(string|int $instansiId, int $perPage = 10): LengthAwarePaginator
    {
        return PeminjamanPita::whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->with(['user', 'user.divisi'])
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate($perPage);
    }

    public function getHaidLogsPaginated(string|int $instansiId, int $perPage = 10): LengthAwarePaginator
    {
        return DataHaid::select('data_haid.*', 'users.nama as user_nama', 'users.nomor_induk', 'divisi.divisi_name')
            ->join('users', 'data_haid.id_user', '=', 'users.id')
            ->leftJoin('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.instansi_id', $instansiId)
            ->orderBy('data_haid.tanggal_mulai', 'desc')
            ->paginate($perPage);
    }

    public function getTotalHaidLogs(string|int $instansiId): int
    {
        return DataHaid::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })->count();
    }

    public function getPitaCounts(string|int $instansiId): array
    {
        $counts = PeminjamanPita::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })->selectRaw("
            COUNT(CASE WHEN status = '" . PitaStatus::DIPINJAM->value . "' THEN 1 END) as aktif,
            COUNT(CASE WHEN status = '" . PitaStatus::DIKEMBALIKAN->value . "' OR status = '" . PitaStatus::KEMBALI->value . "' THEN 1 END) as selesai,
            COUNT(CASE WHEN status = '" . PitaStatus::TERLAMBAT->value . "' THEN 1 END) as terlambat
        ")->first();

        return [
            'aktif' => (int) ($counts->aktif ?? 0),
            'selesai' => (int) ($counts->selesai ?? 0),
            'terlambat' => (int) ($counts->terlambat ?? 0),
        ];
    }

    public function getAverageDuration(string|int $instansiId): float
    {
        $avg = DataHaid::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })->whereNotNull('durasi_hari')->avg('durasi_hari');

        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function getDurationDistribution(string|int $instansiId): array
    {
        $data = DataHaid::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })
        ->selectRaw("
            CASE 
                WHEN durasi_hari < 4 THEN '< 4 Hari'
                WHEN durasi_hari BETWEEN 4 AND 7 THEN '4 - 7 Hari (Normal)'
                ELSE '> 7 Hari'
            END as duration_range,
            COUNT(*) as count
        ")
        ->whereNotNull('durasi_hari')
        ->groupBy('duration_range')
        ->pluck('count', 'duration_range')
        ->toArray();

        return $data;
    }

    public function getOverdueWarnings(string|int $instansiId): Collection
    {
        return PeminjamanPita::with(['user.divisi'])
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->whereIn('status', [PitaStatus::TERLAMBAT->value, PitaStatus::DIPINJAM->value])
            ->whereDate('estimasi_selesai_haid', '<', now()->toDateString())
            ->orderBy('estimasi_selesai_haid', 'asc')
            ->get();
    }

    public function getAllPeminjaman(string|int $instansiId): Collection
    {
        return PeminjamanPita::with('user')
            ->whereHas('user', function ($query) use ($instansiId) {
                $query->where('jk', UserGender::PEREMPUAN->value)
                      ->where('instansi_id', $instansiId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function countPendingVerification(string|int $instansiId): int
    {
        return PeminjamanPita::where('verified', 0)
            ->whereHas('user', function ($query) use ($instansiId) {
                $query->where('instansi_id', $instansiId);
            })
            ->count();
    }

    public function countFemaleMembers(string|int $instansiId): int
    {
        return User::where('level', UserRole::MEMBER->value)
            ->where('jk', UserGender::PEREMPUAN->value)
            ->where('instansi_id', $instansiId)
            ->count();
    }

    public function findPeminjamanOrFail(int|string $id): PeminjamanPita
    {
        return PeminjamanPita::findOrFail($id);
    }

    public function createPeminjaman(array $data): PeminjamanPita
    {
        return PeminjamanPita::create($data);
    }

    public function updatePeminjaman(PeminjamanPita $pita, array $data): bool
    {
        return $pita->update($data);
    }

    public function deletePeminjaman(PeminjamanPita $pita): bool
    {
        return (bool) $pita->delete();
    }

    public function create(array $data): DataHaid
    {
        return DataHaid::create($data);
    }

    public function update(DataHaid $haid, array $data): bool
    {
        return $haid->update($data);
    }

    public function getUserHaidGrouped(int|string $userId): array
    {
        $haid = DataHaid::where('id_user', $userId)
            ->whereHas('user', function ($query) {
                $query->where('jk', UserGender::PEREMPUAN->value);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $grouped = $haid->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_mulai)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_mulai)->format('m');
            })->map(function ($monthGroup, $month) {
                $monthName = Carbon::createFromFormat('m', $month)
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
        })->values()->toArray();
    }

    public function getActiveUserHaid(int|string $userId): ?DataHaid
    {
        return DataHaid::where('id_user', $userId)
            ->where('status', HaidStatus::BERLANGSUNG->value)
            ->first();
    }

    public function getActiveUserPita(int|string $userId): ?PeminjamanPita
    {
        return PeminjamanPita::where('id_user', $userId)
            ->whereIn('status', [PitaStatus::MENUNGGU->value, PitaStatus::DIPINJAM->value, PitaStatus::TERLAMBAT->value])
            ->whereNull('tanggal_kembali')
            ->first();
    }

    public function getUserPitaGrouped(int|string $userId): array
    {
        $data = PeminjamanPita::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = [];
        foreach ($data as $item) {
            $date = date('Y-m-d', strtotime($item->tanggal_pinjam));
            $year = date('Y', strtotime($date));
            $month = date('F', strtotime($date));

            if (!isset($grouped[$year])) {
                $grouped[$year] = ['year' => (int) $year, 'months' => []];
            }

            $monthKey = strtolower($month);
            if (!isset($grouped[$year]['months'][$monthKey])) {
                $grouped[$year]['months'][$monthKey] = ['month' => $monthKey, 'data' => []];
            }

            $grouped[$year]['months'][$monthKey]['data'][] = $item;
        }

        $result = [];
        foreach ($grouped as $year => $yearData) {
            $yearData['months'] = array_values($yearData['months']);
            $result[] = $yearData;
        }

        return $result;
    }

    public function getUserActiveHaidWindow(int|string $userId, string $today): Collection
    {
        return DataHaid::select('data_haid.*')
            ->join('users', 'users.id', '=', 'data_haid.id_user')
            ->where('data_haid.id_user', $userId)
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->orderBy('users.nama', 'asc')
            ->with('user')
            ->get();
    }

    public function countHaidInCurrentMonth(int|string $userId): int
    {
        return DataHaid::where('id_user', $userId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    public function findUserHaidRecord(int|string $id, int|string $userId): ?DataHaid
    {
        return DataHaid::where('id', $id)->where('id_user', $userId)->first();
    }

    public function completeUserHaidRecord(int|string $id, int|string $userId, string $tanggalSelesai, float $durasiHari): void
    {
        DataHaid::where('id', $id)->where('id_user', $userId)->update([
            'tanggal_selesai' => $tanggalSelesai,
            'durasi_hari' => $durasiHari,
            'status' => HaidStatus::SELESAI->value,
            'updated_at' => now(),
        ]);
    }

    public function updateUserPitaEstimasi(int|string $userId, string $estimasi): void
    {
        PeminjamanPita::where('id_user', $userId)
            ->whereNull('tanggal_kembali')
            ->whereIn('status', [PitaStatus::MENUNGGU->value, PitaStatus::DIPINJAM->value, PitaStatus::TERLAMBAT->value])
            ->update([
                'estimasi_selesai_haid' => $estimasi,
                'updated_at' => now(),
            ]);
    }

    public function markOverduePitaForUser(int|string $userId, string $today): void
    {
        PeminjamanPita::where('id_user', $userId)
            ->whereIn('status', [PitaStatus::MENUNGGU->value, PitaStatus::DIPINJAM->value])
            ->whereNull('tanggal_kembali')
            ->whereDate('estimasi_selesai_haid', '<', $today)
            ->update([
                'status' => PitaStatus::TERLAMBAT->value,
                'updated_at' => now()
            ]);
    }
}
