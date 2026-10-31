<?php

namespace App\Modules\User\Member\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\User\Member\Services\MemberService;
use App\Modules\User\Member\Requests\ConsultantDashboardRequest;
use App\Modules\User\Member\Requests\MemberDetailDashboardRequest;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function __construct(
        protected MemberService $memberService
    ) {}

    public function healthConsultant(ConsultantDashboardRequest $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $data = $this->memberService->getHealthConsultantDashboard();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function memberDetail(MemberDetailDashboardRequest $request, string|int $id): JsonResponse
    {
        $user = $request->user();
        
        if ($user->isMember() && $user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $data = $this->memberService->getMemberDetailDashboard($id);
        
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data Member tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
