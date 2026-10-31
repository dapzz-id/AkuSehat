<?php

namespace App\Modules\Divisi\UnitManagement\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Divisi\UnitManagement\Repositories\DivisiRepository;
use App\Modules\Divisi\UnitManagement\Services\DivisiService;
use App\Modules\Divisi\UnitManagement\Requests\GetAllDivisiApiRequest;
use Illuminate\Http\JsonResponse;

class DivisiApiController extends Controller
{
    public function __construct(
        protected DivisiRepository $divisiRepository,
        protected DivisiService $divisiService
    ) {}

    public function getAllDivisi(GetAllDivisiApiRequest $request): JsonResponse
    {
        $result = $this->divisiService->getAllDivisiForApi($request->user());

        if (!$result['status']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 403);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
}
