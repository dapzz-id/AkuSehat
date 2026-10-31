<?php

namespace App\Modules\User\ManageUser\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\User\ManageUser\Repositories\UserRepository;
use App\Modules\User\ManageUser\Services\UserService;
use App\Modules\User\ManageUser\Requests\GetMemberApiRequest;
use App\Modules\User\ManageUser\Requests\GetMemberByDivisiApiRequest;
use Illuminate\Http\JsonResponse;

class UserApiController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserService $userService
    ) {}

    public function getMember(GetMemberApiRequest $request): JsonResponse
    {
        $result = $this->userService->getMembersForApi($request->user());

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

    public function getMemberByDivisi(GetMemberByDivisiApiRequest $request, string|int $divisi_id): JsonResponse
    {
        $result = $this->userService->getMembersByDivisiForApi($request->user(), $divisi_id);

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
