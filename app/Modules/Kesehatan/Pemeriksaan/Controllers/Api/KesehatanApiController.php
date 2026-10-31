<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Pemeriksaan\Repositories\KesehatanRepository;
use App\Modules\Kesehatan\Pemeriksaan\Services\KesehatanService;
use App\Modules\Kesehatan\Pemeriksaan\Requests\GetKesehatanMemberRequest;
use Illuminate\Http\JsonResponse;

class KesehatanApiController extends Controller
{
    public function __construct(
        protected KesehatanRepository $kesehatanRepository,
        protected KesehatanService $kesehatanService
    ) {}

    public function getKesehatanMember(GetKesehatanMemberRequest $request, string|int $id): JsonResponse
    {
        $result = $this->kesehatanService->getMemberKesehatanHistory($request->user(), $id);

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
