<?php

namespace App\Modules\Hb\PemeriksaanHb\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Hb\PemeriksaanHb\Repositories\HbRepository;
use App\Modules\Hb\PemeriksaanHb\Services\HbService;
use App\Modules\Hb\PemeriksaanHb\Requests\GetHbMemberRequest;
use Illuminate\Http\JsonResponse;

class HbApiController extends Controller
{
    public function __construct(
        protected HbRepository $hbRepository,
        protected HbService $hbService
    ) {}

    public function getHbMember(GetHbMemberRequest $request, string|int $id): JsonResponse
    {
        $result = $this->hbService->getMemberHbHistory($request->user(), $id);

        if (!$result['status']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
}
