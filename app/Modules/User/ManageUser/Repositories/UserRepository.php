<?php

namespace App\Modules\User\ManageUser\Repositories;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function getQueryForInstansi(string $instansiId): Builder
    {
        return User::with(['divisi', 'kesehatan', 'hb'])
            ->select('users.*')
            ->leftJoin('divisi', 'divisi.id', '=', 'users.id_divisi')
            ->where('users.instansi_id', $instansiId);
    }

    public function findById(string|int $id): ?User
    {
        return User::find($id);
    }

    public function findOrFail(string|int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function deleteWithRelations(User $user): bool
    {
        $user->dataHaid()->delete();
        $user->kesehatan()->delete();
        $user->hb()->delete();
        return (bool) $user->delete();
    }

    public function countUsersInDivisi(string $instansiId): int
    {
        return User::where('instansi_id', $instansiId)
            ->where('level', '!=', UserRole::ADMIN_INSTANSI->value)
            ->whereNotNull('id_divisi')
            ->count();
    }

    public function getMembersByInstansi(int|string $instansiId, int $perPage = 15): LengthAwarePaginator
    {
        return User::with('divisi')
            ->where('level', UserRole::MEMBER->value)
            ->where('instansi_id', $instansiId)
            ->paginate($perPage);
    }

    public function getMembersByDivisi(int|string $instansiId, int|string $divisiId): Collection
    {
        return User::with('divisi')
            ->where('level', UserRole::MEMBER->value)
            ->where('instansi_id', $instansiId)
            ->where('id_divisi', $divisiId)
            ->get();
    }

    public function getDivisionsForInstansi(string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }

    public function massDeleteUsers(string $instansiId, array $ids): int
    {
        return User::whereIn('id', $ids)
            ->where('instansi_id', $instansiId)
            ->delete();
    }

    public function massUpdateDivisi(string $instansiId, array $ids, ?string $divisiId): int
    {
        return User::whereIn('id', $ids)
            ->where('instansi_id', $instansiId)
            ->update(['id_divisi' => $divisiId]);
    }

    public function getAllInstansi(): Collection
    {
        return Instansi::orderBy('nama_instansi')->get();
    }

    public function getAllDivisions(): Collection
    {
        return Divisi::all();
    }

    public function getSuperAdminFilteredUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('instansi');

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        $instansiId = $filters['instansi_id'] ?? ($filters['sekolah_id'] ?? null);
        if (!empty($instansiId)) {
            $query->where('instansi_id', $instansiId);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getFilteredUsers(string $instansiId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $activeTab = $filters['tab'] ?? 'member';

        $query = User::with(['divisi', 'kesehatan', 'hb', 'instansi'])
            ->where('instansi_id', $instansiId);

        if ($activeTab === 'siswa' || $activeTab === 'member') {
            $query->where('level', UserRole::MEMBER->value)
                ->where(function ($q) {
                    $q->whereNull('id_divisi')
                      ->orWhereHas('divisi', function ($d) {
                          $d->whereNull('kategori')
                            ->orWhere('kategori', Divisi::KATEGORI_MEMBER)
                            ->orWhereNotIn('kategori', [Divisi::KATEGORI_STAF, Divisi::KATEGORI_PIMPINAN]);
                      });
                });
        } elseif ($activeTab === 'guru' || $activeTab === 'staf') {
            $query->where('level', UserRole::MEMBER->value)
                ->whereHas('divisi', function ($d) {
                    $d->where('kategori', Divisi::KATEGORI_STAF);
                });
        } elseif ($activeTab === 'petugas') {
            $query->whereIn('level', [
                UserRole::ADMIN->value,
                UserRole::HEALTH_MONITOR->value,
                UserRole::HEALTH_CONSULTANT->value,
            ]);
        } elseif ($activeTab === 'pimpinan') {
            $query->where(function ($q) {
                $q->where('level', UserRole::ADMIN_INSTANSI->value)
                  ->orWhere(function ($sub) {
                      $sub->where('level', UserRole::MEMBER->value)
                          ->whereHas('divisi', function ($d) {
                              $d->where('kategori', Divisi::KATEGORI_PIMPINAN);
                          });
                  });
            });
        } elseif ($activeTab === 'all') {
            // Tampilkan semua warga / anggota instansi
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['divisi'])) {
            if ($filters['divisi'] === 'none' || $filters['divisi'] === 'alumni') {
                $query->whereNull('id_divisi');
            } else {
                $query->where('id_divisi', $filters['divisi']);
            }
        }

        if (!empty($filters['jk']) && $filters['jk'] !== 'all') {
            $query->where('jk', $filters['jk']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        return $query->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();
    }

    public function getUserStats(string $instansiId): array
    {
        $total = User::where('instansi_id', $instansiId)->count();

        $siswaCount = User::where('instansi_id', $instansiId)
            ->where('level', UserRole::MEMBER->value)
            ->where(function ($q) {
                $q->whereNull('id_divisi')
                  ->orWhereHas('divisi', function ($d) {
                      $d->whereNull('kategori')
                        ->orWhere('kategori', Divisi::KATEGORI_MEMBER)
                        ->orWhereNotIn('kategori', [Divisi::KATEGORI_STAF, Divisi::KATEGORI_PIMPINAN]);
                  });
            })
            ->count();

        $guruCount = User::where('instansi_id', $instansiId)
            ->where('level', UserRole::MEMBER->value)
            ->whereHas('divisi', function ($d) {
                $d->where('kategori', Divisi::KATEGORI_STAF);
            })
            ->count();

        $pimpinanCount = User::where('instansi_id', $instansiId)
            ->where(function ($q) {
                $q->where('level', UserRole::ADMIN_INSTANSI->value)
                  ->orWhere(function ($sub) {
                      $sub->where('level', UserRole::MEMBER->value)
                          ->whereHas('divisi', function ($d) {
                              $d->where('kategori', Divisi::KATEGORI_PIMPINAN);
                          });
                  });
            })
            ->count();

        $petugasCount = User::where('instansi_id', $instansiId)
            ->whereIn('level', [
                UserRole::ADMIN->value,
                UserRole::HEALTH_MONITOR->value,
                UserRole::HEALTH_CONSULTANT->value,
            ])
            ->count();

        $totalLaki = User::where('instansi_id', $instansiId)->where('jk', 'L')->count();
        $totalPerempuan = User::where('instansi_id', $instansiId)->where('jk', 'P')->count();
        $totalKesehatan = Kesehatan::where('instansi_id', $instansiId)->count() + Hb::where('instansi_id', $instansiId)->count();

        return [
            'counts' => [
                'total' => $total,
                'siswa' => $siswaCount,
                'member' => $siswaCount,
                'guru' => $guruCount,
                'staf' => $guruCount,
                'pimpinan' => $pimpinanCount,
                'petugas' => $petugasCount,
                'all' => $total,
            ],
            'userStats' => (object) [
                'total' => $total,
                'total_laki' => $totalLaki,
                'total_perempuan' => $totalPerempuan,
                'total_kesehatan' => $totalKesehatan,
                'active' => User::where('instansi_id', $instansiId)->where('status', UserStatus::AKTIF->value)->count(),
                'suspended' => User::where('instansi_id', $instansiId)->where('status', UserStatus::SUSPENDED->value)->count(),
                'banned' => User::where('instansi_id', $instansiId)->where('status', UserStatus::BANNED->value)->count(),
            ],
        ];
    }
}
