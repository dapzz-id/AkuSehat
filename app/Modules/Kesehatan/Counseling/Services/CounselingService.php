<?php

namespace App\Modules\Kesehatan\Counseling\Services;

use App\Modules\Kesehatan\Counseling\Repositories\CounselingRepository;
use App\Modules\Kesehatan\Counseling\Enums\CounselingSessionStatus;
use App\Models\CounselingSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CounselingService
{
    public function __construct(
        protected CounselingRepository $counselingRepository
    ) {}

    public function getDashboardData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;
        $status = $filters['status'] ?? null;
        $divisiId = $filters['divisi_id'] ?? null;
        $search = $filters['search'] ?? null;

        $counts = $this->counselingRepository->getStatusCounts($instansiId, $status, $divisiId, $search);
        $totalSessions = $counts['total'];
        $pendingSessions = $counts['pending'];
        $scheduledSessions = $counts['scheduled'];
        $completedSessions = $counts['completed'];
        $cancelledSessions = $counts['cancelled'];

        $statusChartData = [
            'labels' => [
                CounselingSessionStatus::MENUNGGU->label(),
                CounselingSessionStatus::TERJADWAL->label(),
                CounselingSessionStatus::SELESAI->label(),
                CounselingSessionStatus::DIBATALKAN->label(),
            ],
            'data' => [$pendingSessions, $scheduledSessions, $completedSessions, $cancelledSessions],
            'colors' => [
                CounselingSessionStatus::MENUNGGU->color(),
                CounselingSessionStatus::TERJADWAL->color(),
                CounselingSessionStatus::SELESAI->color(),
                CounselingSessionStatus::DIBATALKAN->color(),
            ]
        ];

        $topicStats = $this->counselingRepository->getTopicStats($instansiId);
        $topicChartData = [
            'labels' => $topicStats->pluck('topic')->toArray(),
            'data' => $topicStats->pluck('total')->toArray(),
        ];

        $sessions = $this->counselingRepository->getPaginatedSessions($instansiId, $status, $divisiId, $search, 15);
        $divisiList = $this->counselingRepository->getInstansiDivisions($instansiId);
        $divisions = $divisiList;
        $consultants = $this->counselingRepository->getInstansiConsultants($instansiId);
        $members = $this->counselingRepository->getInstansiMembers($instansiId);
        $instansi = $actor->instansi;

        return compact(
            'totalSessions', 'pendingSessions', 'scheduledSessions', 'completedSessions', 'cancelledSessions',
            'statusChartData', 'topicChartData', 'sessions', 'divisiList', 'divisions', 'consultants', 'members', 'instansi'
        );
    }

    public function storeAdminSession(User $actor, array $validated): CounselingSession
    {
        return $this->counselingRepository->createSession([
            'instansi_id' => $actor->instansi_id,
            'user_id' => $validated['user_id'],
            'consultant_id' => $validated['consultant_id'] ?? $actor->id,
            'session_date' => $validated['session_date'],
            'topic' => $validated['topic'],
            'notes' => $validated['notes'] ?? null,
            'status' => CounselingSessionStatus::TERJADWAL->value,
            'is_confidential' => (bool) ($validated['is_confidential'] ?? true),
        ]);
    }

    public function updateSessionStatus(CounselingSession $counseling, array $validated): bool
    {
        return $this->counselingRepository->updateSession($counseling, [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $counseling->notes,
        ]);
    }

    public function approveSession(
        CounselingSession $session,
        User $consultant,
        ?string $scheduledAt = null,
        ?string $notes = null,
        ?string $ruangan = null
    ): CounselingSession {
        $session->update([
            'status' => CounselingSessionStatus::TERJADWAL->value,
            'consultant_id' => $consultant->id,
            'scheduled_at' => $scheduledAt ?? $session->scheduled_at ?? $session->session_date,
            'ruangan' => $ruangan ?? $session->ruangan,
            'notes' => $notes ?? $session->notes,
        ]);

        return $session;
    }

    public function rejectSession(CounselingSession $session, User $consultant, ?string $reason = null): CounselingSession
    {
        $session->update([
            'status' => CounselingSessionStatus::DIBATALKAN->value,
            'consultant_id' => $consultant->id,
            'notes' => $reason ? ($session->notes ? $session->notes . ' | Alasan: ' . $reason : 'Ditolak: ' . $reason) : $session->notes,
        ]);

        return $session;
    }

    public function completeSession(CounselingSession $session, User $consultant, ?string $summaryNotes = null): CounselingSession
    {
        $session->update([
            'status' => CounselingSessionStatus::SELESAI->value,
            'consultant_id' => $consultant->id,
            'notes' => $summaryNotes ?? $session->notes,
        ]);

        return $session;
    }

    public function bookSession(User $user, array $validated): CounselingSession
    {
        return CounselingSession::create([
            'instansi_id' => $user->instansi_id,
            'user_id' => $user->id,
            'consultant_id' => $validated['consultant_id'] ?? null,
            'session_date' => $validated['session_date'],
            'topic' => $validated['topic'],
            'notes' => $validated['notes'] ?? null,
            'status' => CounselingSessionStatus::MENUNGGU->value,
            'is_confidential' => $validated['is_confidential'] ?? true,
        ]);
    }

    public function cancelSession(User $user, string|int $id): array
    {
        $session = CounselingSession::where('id', $id)
            ->where('user_id', $user->id)
            ->where('instansi_id', $user->instansi_id)
            ->first();

        if (!$session) {
            return ['status' => false, 'code' => 404, 'message' => 'Sesi konsultasi tidak ditemukan.'];
        }

        if ($session->status !== CounselingSessionStatus::MENUNGGU->value) {
            return ['status' => false, 'code' => 422, 'message' => 'Hanya jadwal dengan status menunggu konfirmasi yang dapat dibatalkan.'];
        }

        $session->update(['status' => CounselingSessionStatus::DIBATALKAN->value]);

        return ['status' => true, 'code' => 200, 'message' => 'Jadwal konsultasi berhasil dibatalkan.'];
    }

    public function getMemberSessions(User $user): Collection
    {
        return $this->counselingRepository->getUserSessions($user->id, $user->instansi_id);
    }

    public function getHealthSummary(User $user): array
    {
        $latest = $this->counselingRepository->getLatestKesehatanForInstansi($user->instansi_id);

        $total = 0; $ob = 0; $kr = 0; $nm = 0;
        foreach ($latest as $k) {
            $total++;
            if (str_contains(strtolower($k->status ?? ''), 'obesitas')) $ob++;
            elseif (str_contains(strtolower($k->status ?? ''), 'kurus') || str_contains(strtolower($k->status ?? ''), 'underweight')) $kr++;
            else $nm++;
        }

        return [
            'total_students' => $total,
            'normal' => $nm,
            'obese' => $ob,
            'underweight' => $kr,
        ];
    }
}
