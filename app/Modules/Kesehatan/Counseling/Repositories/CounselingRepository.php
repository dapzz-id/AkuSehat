<?php

namespace App\Modules\Kesehatan\Counseling\Repositories;

use App\Models\CounselingSession;
use App\Models\Kesehatan;
use App\Models\User;
use App\Models\Divisi;
use App\Modules\Kesehatan\Counseling\Enums\CounselingSessionStatus;
use App\Modules\User\Enums\UserRole;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BmiStatus;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BloodPressureStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CounselingRepository
{
    public function getSessionsForAdmin(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null, ?string $consultantId = null)
    {
        $query = CounselingSession::with(['user.divisi', 'consultant'])
            ->where('instansi_id', $instansiId);

        if (!empty($consultantId)) {
            $query->where(function ($q) use ($consultantId) {
                $q->where('consultant_id', $consultantId)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('consultant_id')
                         ->where('status', CounselingSessionStatus::MENUNGGU->value);
                  });
            });
        }

        if (!empty($divisiId)) {
            $query->whereHas('user', function ($q) use ($divisiId) {
                $q->where('id_divisi', $divisiId);
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function getPaginatedSessions(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null, int $perPage = 15, ?string $consultantId = null): LengthAwarePaginator
    {
        return $this->getSessionsForAdmin($instansiId, $status, $divisiId, $search, $consultantId)
            ->orderBy('counseling_sessions.session_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStatusCounts(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null, ?string $consultantId = null): array
    {
        $query = $this->getSessionsForAdmin($instansiId, $status, $divisiId, $search, $consultantId);

        $countQuery = (clone $query)->reorder();
        $countQuery->getQuery()->columns = null;

        $counts = $countQuery->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = '" . CounselingSessionStatus::MENUNGGU->value . "' THEN 1 END) as pending,
            COUNT(CASE WHEN status = '" . CounselingSessionStatus::TERJADWAL->value . "' THEN 1 END) as scheduled,
            COUNT(CASE WHEN status = '" . CounselingSessionStatus::SELESAI->value . "' THEN 1 END) as completed,
            COUNT(CASE WHEN status = '" . CounselingSessionStatus::DIBATALKAN->value . "' THEN 1 END) as cancelled
        ")->first();

        return [
            'total' => (int) ($counts->total ?? 0),
            'pending' => (int) ($counts->pending ?? 0),
            'scheduled' => (int) ($counts->scheduled ?? 0),
            'completed' => (int) ($counts->completed ?? 0),
            'cancelled' => (int) ($counts->cancelled ?? 0),
        ];
    }

    public function getTopicStats(int|string $instansiId)
    {
        return CounselingSession::where('instansi_id', $instansiId)
            ->select('topic', DB::raw('count(*) as total'))
            ->groupBy('topic')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }

    public function getInstansiDivisions(int|string $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)->orderBy('divisi_name')->get();
    }

    public function getInstansiConsultants(int|string $instansiId): Collection
    {
        return User::where('instansi_id', $instansiId)
            ->whereIn('level', [
                UserRole::HEALTH_CONSULTANT->value,
                UserRole::HEALTH_MONITOR->value,
                UserRole::ADMIN->value,
                UserRole::ADMIN_INSTANSI->value
            ])
            ->get();
    }

    public function getInstansiMembers(int|string $instansiId): Collection
    {
        return User::with('divisi')
            ->where('instansi_id', $instansiId)
            ->where('level', UserRole::MEMBER->value)
            ->orderBy('nama')
            ->get();
    }

    public function createSession(array $data): CounselingSession
    {
        return CounselingSession::create($data);
    }

    public function updateSession(CounselingSession $session, array $data): bool
    {
        return $session->update($data);
    }

    public function findOrFail(int|string $id): CounselingSession
    {
        return CounselingSession::findOrFail($id);
    }

    public function getLatestKesehatanForInstansi(int|string $instansiId): Collection
    {
        return Kesehatan::query()
            ->whereHas('user', function($q) use ($instansiId) {
                $q->where('level', UserRole::MEMBER->value)
                  ->where('instansi_id', $instansiId);
            })
            ->whereYear('tgl', now()->year)
            ->join(DB::raw('(select id_user, max(tgl) as max_tgl from kesehatan group by id_user) as lastk'),
                function($join) {
                    $join->on('kesehatan.id_user', '=', 'lastk.id_user')
                         ->on('kesehatan.tgl', '=', 'lastk.max_tgl');
                }
            )
            ->select('kesehatan.*')
            ->get();
    }

    public function getSessionsForConsultant(int|string $instansiId, int|string|null $consultantId = null, ?string $status = null, ?string $search = null): Collection
    {
        $query = CounselingSession::where('instansi_id', $instansiId)
            ->with(['user.divisi', 'consultant'])
            ->orderBy('created_at', 'desc');

        if (!empty($consultantId)) {
            $query->where(function ($q) use ($consultantId) {
                $q->where('consultant_id', $consultantId)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('consultant_id')
                         ->where('status', CounselingSessionStatus::MENUNGGU->value);
                  });
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        return $query->get();
    }

    public function getUserSessions(int|string $userId, int|string $instansiId): Collection
    {
        return CounselingSession::where('user_id', $userId)
            ->where('instansi_id', $instansiId)
            ->with(['consultant:id,nama,level'])
            ->orderBy('session_date', 'desc')
            ->get();
    }

    public function getPendingCount(int|string $instansiId, int|string|null $consultantId = null): int
    {
        $query = CounselingSession::where('instansi_id', $instansiId)
            ->where('status', CounselingSessionStatus::MENUNGGU->value);

        if (!empty($consultantId)) {
            $query->where(function ($q) use ($consultantId) {
                $q->where('consultant_id', $consultantId)
                  ->orWhereNull('consultant_id');
            });
        }

        return $query->count();
    }

    public function getHealthNotifications(int|string $instansiId, int $limit = 30): Collection
    {
        return Kesehatan::with('user')
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId)->where('level', UserRole::MEMBER->value);
            })
            ->where(function ($q) {
                $q->whereIn('status', [BmiStatus::OBESITAS->value, BmiStatus::KURUS->value])
                  ->orWhereIn('status_darah', [
                      BloodPressureStatus::HIPOTENSI->value,
                      BloodPressureStatus::HIPERTENSI_1->value,
                      BloodPressureStatus::HIPERTENSI_2->value,
                  ]);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getDivisionsWithMemberCount(int|string $instansiId): Collection
    {
        $divisions = Divisi::where('instansi_id', $instansiId)
            ->orderBy('divisi_name', 'asc')
            ->get();

        $members = User::where('instansi_id', $instansiId)
            ->where('level', UserRole::MEMBER->value)
            ->with(['kesehatan' => function ($q) {
                $q->orderBy('tgl', 'desc')->orderBy('created_at', 'desc');
            }])
            ->get()
            ->groupBy('id_divisi');

        return $divisions->map(function ($divisi) use ($members) {
            $divMembers = $members->get($divisi->id, collect());
            $totalMember = $divMembers->count();

            $obesitas = 0;
            $kurus = 0;
            $normal = 0;

            $dataMember = $divMembers->map(function ($member) use ($divisi, &$obesitas, &$kurus, &$normal) {
                $latest = $member->kesehatan->first();
                $avgBmi = $member->kesehatan->isNotEmpty() ? round((float) $member->kesehatan->avg('imt'), 1) : 0.0;

                $statusStr = strtolower($latest?->status ?? '');
                if (str_contains($statusStr, 'obesitas')) {
                    $obesitas++;
                } elseif (str_contains($statusStr, 'kurus') || str_contains($statusStr, 'underweight')) {
                    $kurus++;
                } else {
                    $normal++;
                }

                $riwayatKesehatan = $member->kesehatan->map(function ($k) {
                    $tglStr = $k->tgl instanceof \Carbon\Carbon ? $k->tgl->format('Y-m-d') : (string) ($k->tgl ?? '');
                    return [
                        'tgl' => $tglStr,
                        'bb' => (string) ($k->bb ?? ''),
                        'tb' => (string) ($k->tb ?? ''),
                        'score' => (float) ($k->imt ?? 0),
                        'status' => (string) ($k->status ?? 'Normal'),
                        'status_darah' => (string) ($k->status_darah ?? 'Normal'),
                        'perilaku_beresiko' => (string) ($k->perilaku_beresiko ?? ''),
                        'gangguan_reproduksi' => (string) ($k->gangguan_reproduksi ?? ''),
                    ];
                })->values()->all();

                $pemeriksaanTerakhir = null;
                if ($latest) {
                    $pemeriksaanTerakhir = $latest->tgl instanceof \Carbon\Carbon
                        ? $latest->tgl->format('Y-m-d')
                        : (string) ($latest->tgl ?? '');
                }

                return [
                    'id' => $member->id,
                    'nama' => (string) $member->nama,
                    'nomor_induk' => (string) ($member->nomor_induk ?? ''),
                    'divisi' => (string) $divisi->divisi_name,
                    'jk' => (string) ($member->jk ?? 'L'),
                    'username' => (string) ($member->username ?? ''),
                    'pemeriksaan_terakhir' => $pemeriksaanTerakhir,
                    'avg_bmi' => $avgBmi,
                    'riwayat_kesehatan' => $riwayatKesehatan,
                ];
            })->values()->all();

            return [
                'id' => $divisi->id,
                'divisi' => $divisi->divisi_name,
                'nama_divisi' => $divisi->divisi_name,
                'divisi_name' => $divisi->divisi_name,
                'total_member' => $totalMember,
                'obesitas' => $obesitas,
                'kurus' => $kurus,
                'normal' => $normal,
                'data_member' => $dataMember,
            ];
        });
    }
}
