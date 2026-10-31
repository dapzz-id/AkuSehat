<?php

namespace App\Modules\User\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\User\Auth\Services\AuthService;
use App\Modules\User\Auth\Repositories\AuthRepository;
use App\Modules\User\Auth\Requests\ApiLoginRequest;
use App\Modules\User\Auth\Requests\GoogleLoginRequest;
use App\Modules\User\Auth\Requests\CheckAuthRequest;
use App\Modules\User\Auth\Requests\LinkInstansiRequest;
use App\Modules\User\Auth\Requests\LogoutRequest;
use App\Modules\User\Auth\Requests\RegisterPersonalRequest;
use Illuminate\Http\JsonResponse;

class AuthApiController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuthRepository $authRepository
    ) {}

    public function registerPersonal(RegisterPersonalRequest $request): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Pendaftaran mandiri dinonaktifkan. Akun siswa/member dibuat dan dikelola langsung oleh Admin Institusi / Instansi terkait.'
        ], 403);
    }

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->authService->attemptApiLogin(
            $validated['login'],
            $validated['password'],
            $validated['device_name'] ?? 'mobile_app'
        );

        $code = $result['code'] ?? 200;
        unset($result['code']);

        return response()->json($result, $code);
    }

    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->authService->handleGoogleLoginApi(
            $validated['email'],
            $validated['name'],
            $validated['google_id'],
            $validated['avatar'] ?? null,
            $validated['id_token'] ?? null
        );

        $code = $result['code'] ?? 200;
        unset($result['code']);

        return response()->json($result, $code);
    }

    public function checkAuth(CheckAuthRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Token valid',
            'user' => $request->user()->load('instansi', 'divisi'),
        ]);
    }

    public function linkInstansi(LinkInstansiRequest $request): JsonResponse
    {
        $result = $this->authService->linkInstansi($request->user(), $request->validated('invite_code'));

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'instansi' => $result['instansi'],
        ]);
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        $this->authService->logoutApi($request->user());

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil. Sesi mobile telah diakhiri.',
        ]);
    }
}
