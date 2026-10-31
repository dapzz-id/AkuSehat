<?php

namespace App\Modules\User\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Modules\User\Auth\Services\AuthService;
use App\Modules\User\Auth\Repositories\AuthRepository;
use App\Modules\User\Auth\Requests\LoginRequest;
use App\Modules\User\Auth\Requests\LinkInstansiRequest;
use App\Modules\User\Auth\Requests\RegisterRequest;
use App\Modules\User\Auth\Requests\GoogleCallbackRequest;
use App\Modules\User\Auth\Requests\LogoutRequest;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuthRepository $authRepository
    ) {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->attemptWebLogin(
            $request->input('username'),
            $request->input('password'),
            $request->filled('remember'),
            $request
        );

        if ($result['success']) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['username' => $result['message']]);
    }

    public function showRegisterForm()
    {
        return redirect()->route('login')->with('warning', 'Pendaftaran mandiri dinonaktifkan. Akun siswa/anggota dibuat dan dikelola langsung oleh Admin Institusi / Instansi Anda.');
    }

    public function register(RegisterRequest $request)
    {
        return redirect()->route('login')->with('warning', 'Pendaftaran mandiri dinonaktifkan. Akun siswa/anggota dibuat dan dikelola langsung oleh Admin Institusi / Instansi Anda.');
    }

    public function linkInstansi(LinkInstansiRequest $request)
    {
        $result = $this->authService->linkInstansi(Auth::user(), $request->validated('invite_code'));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function redirectToGoogle()
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $redirectUri = env('GOOGLE_REDIRECT_URI', url('/auth/google/callback'));

        if (empty($clientId)) {
            return view('auth.google-simulate');
        }

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleGoogleCallback(GoogleCallbackRequest $request)
    {
        // Keamanan: Mode simulasi hanya diizinkan di environment local/testing saat debug aktif
        if (app()->environment('local', 'testing') && config('app.debug') && $request->has('simulated') && $request->simulated == '1') {
            $email = strtolower(trim($request->input('email', '')));
            $name = $request->input('name', 'Google User');
            $googleId = 'simulated_' . md5($email);
            return $this->loginWithGoogleUser($googleId, $name, $email, null);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('login')->with('error', 'Login Google dibatalkan atau terjadi kesalahan.');
        }

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = env('GOOGLE_REDIRECT_URI', url('/auth/google/callback'));

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                return redirect()->route('login')->with('error', 'Gagal memverifikasi token Google.');
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;

            $userInfoResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if (!$userInfoResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal mengambil data profil Google.');
            }

            $profile = $userInfoResponse->json();
            $googleId = $profile['sub'];
            $name = $profile['name'] ?? 'Pengguna Google';
            $email = strtolower(trim($profile['email']));
            $avatar = $profile['picture'] ?? null;

            return $this->loginWithGoogleUser($googleId, $name, $email, $avatar);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat autentikasi Google: ' . $e->getMessage());
        }
    }

    protected function loginWithGoogleUser(string $googleId, string $name, string $email, ?string $avatar = null)
    {
        $result = $this->authService->handleGoogleLoginWeb($googleId, $name, $email, $avatar);

        if (!$result['success']) {
            return redirect()->route('login')->withErrors([
                'username' => $result['message']
            ])->with('error', $result['message']);
        }

        return redirect('/dashboard')->with('success', 'Berhasil masuk dengan akun Google!');
    }

    public function logout(LogoutRequest $request)
    {
        $this->authService->logoutWeb($request);
        return redirect('/login');
    }
}
