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
    public function getSessionsForAdmin(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null)
    {
        $query = CounselingSession::with(['user.divisi', 'consultant'])
            ->where('instansi_id', $instansiId);

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

    public function getPaginatedSessions(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getSessionsForAdmin($instansiId, $status, $divisiId, $search)
            ->orderBy('counseling_sessions.session_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStatusCounts(int|string $instansiId, ?string $status = null, ?string $divisiId = null, ?string $search = null): array
    {
        $query = $this->getSessionsForAdmin($instansiId, $status, $divisiId, $search);

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
            ->whereIn('level', [UserRole::HEALTH_CONSULTANT->value, UserRole::ADMIN->value, UserRole::ADMIN_INSTANSI->value])
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

    public function getSessionsForConsultant(int|string $instansiId, ?string $status = null, ?string $search = null): Collection
    {
        $query = CounselingSession::where('instansi_id', $instansiId)
            ->with(['user.divisi', 'consultant'])
            ->orderBy('created_at', 'desc');

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

    public function getPendingCount(int|string $instansiId): int
    {
        return CounselingSession::where('instansi_id', $instansiId)
            ->where('status', CounselingSessionStatus::MENUNGGU->value)
            ->count();
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
        return Divisi::where('instansi_id', $instansiId)
            ->withCount(['users as total_member' => function($q) {
                $q->where('level', UserRole::MEMBER->value);
            }])
            ->get();
    }
}
