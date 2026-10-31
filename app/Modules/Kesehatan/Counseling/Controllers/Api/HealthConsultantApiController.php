<?php

namespace App\Modules\Kesehatan\Counseling\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Counseling\Repositories\CounselingRepository;
use App\Modules\Kesehatan\Counseling\Services\CounselingService;
use App\Modules\Kesehatan\Counseling\Requests\ConsultantSummaryRequest;
use App\Modules\Kesehatan\Counseling\Requests\ConsultantDivisionRequest;
use App\Modules\Kesehatan\Counseling\Requests\ConsultantNotificationRequest;
use App\Modules\Kesehatan\Counseling\Requests\ListCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\ApproveCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\RejectCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\CompleteCounselingRequest;
use Illuminate\Http\JsonResponse;

class HealthConsultantApiController extends Controller
{
    public function __construct(
        protected CounselingRepository $counselingRepository,
        protected CounselingService $counselingService
    ) {}

    public function healthSummary(ConsultantSummaryRequest $req): JsonResponse
    {
        $data = $this->counselingService->getHealthSummary($req->user());

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function healthByDivision(ConsultantDivisionRequest $req): JsonResponse
    {
        $user = $req->user();
        $divisi = $this->counselingRepository->getDivisionsWithMemberCount($user->instansi_id);

        return response()->json([
            'status' => true,
            'data' => $divisi,
        ]);
    }

    public function healthNotification(ConsultantNotificationRequest $req): JsonResponse
    {
        $instansiId = $req->user()->instansi_id;

        $notifications = $this->counselingRepository->getHealthNotifications($instansiId, 30);
        $pendingCount = $this->counselingRepository->getPendingCount($instansiId);

        return response()->json([
            'status' => true,
            'data' => $notifications,
            'pending_counseling_count' => $pendingCount,
        ]);
    }

    public function listCounseling(ListCounselingRequest $req): JsonResponse
    {
        $sessions = $this->counselingRepository->getSessionsForConsultant(
            $req->user()->instansi_id,
            $req->validated('status'),
            $req->validated('search')
        );

        return response()->json([
            'status' => true,
            'data' => $sessions,
        ]);
    }

    public function approveCounseling(ApproveCounselingRequest $req, string|int $id): JsonResponse
    {
        $session = $this->counselingRepository->findOrFail($id);
        $approved = $this->counselingService->approveSession(
            $session,
            $req->user(),
            $req->validated('scheduled_at'),
            $req->validated('notes'),
            $req->validated('ruangan')
        );

        return response()->json([
            'status' => true,
            'message' => 'Konseling berhasil disetujui.',
            'data' => $approved,
        ]);
    }

    public function rejectCounseling(RejectCounselingRequest $req, string|int $id): JsonResponse
    {
        $session = $this->counselingRepository->findOrFail($id);
        $rejected = $this->counselingService->rejectSession(
            $session,
            $req->user(),
            $req->validated('alasan') ?? $req->validated('notes')
        );

        return response()->json([
            'status' => true,
            'message' => 'Konseling telah ditolak.',
            'data' => $rejected,
        ]);
    }

    public function completeCounseling(CompleteCounselingRequest $req, string|int $id): JsonResponse
    {
        $session = $this->counselingRepository->findOrFail($id);
        $completed = $this->counselingService->completeSession(
            $session,
            $req->user(),
            $req->validated('notes') ?? $req->validated('hasil_konseling')
        );

        return response()->json([
            'status' => true,
            'message' => 'Konseling telah diselesaikan.',
            'data' => $completed,
        ]);
    }
}
