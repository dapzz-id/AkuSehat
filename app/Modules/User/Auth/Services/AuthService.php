<?php

namespace App\Modules\User\Auth\Services;

use App\Models\User;
use App\Models\MaintenanceMode;
use App\Modules\User\Enums\UserRole;
use App\Services\AuditLogger;
use App\Modules\User\Auth\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepository
    ) {}

    public function attemptWebLogin(string $loginInput, string $password, bool $remember, Request $request): array
    {
        $loginInput = trim($loginInput);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (MaintenanceMode::isWebInMaintenance() && !$user->isSuperAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                return [
                    'success' => false,
                    'message' => 'Website sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan masuk saat ini.'
                ];
            }

            if ($user->isBanned() || $user->isSuspended() || $user->status === 'nonaktif') {
                Auth::logout();
                $request->session()->invalidate();
                return [
                    'success' => false,
                    'message' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami atau akun tidak dapat diakses.'
                ];
            }

            if ($user->isMember() && $user->instansi_id && (is_null($user->id_divisi) || !$user->divisi)) {
                Auth::logout();
                $request->session()->invalidate();
                return [
                    'success' => false,
                    'message' => 'Akun Anda tidak memiliki divisi/unit aktif atau divisi sebelumnya telah dihapus oleh administrator. Silakan hubungi Admin Institusi/Instansi Anda.'
                ];
            }

            $request->session()->regenerate();

            AuditLogger::auth(
                'web_login_password',
                "Pengguna {$user->name} ({$user->email}) berhasil login via Web menggunakan kredensial akun.",
                ['role' => $user->level, 'instansi_id' => $user->instansi_id]
            );

            return ['success' => true, 'user' => $user];
        }

        return [
            'success' => false,
            'message' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami atau akun tidak dapat diakses.'
        ];
    }

    public function attemptApiLogin(string $loginInput, string $password, ?string $deviceName = 'mobile_app'): array
    {
        $user = $this->authRepository->findByLogin($loginInput);

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'status' => false,
                'code' => 401,
                'message' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami atau akun tidak dapat diakses.'
            ];
        }

        if (MaintenanceMode::isMobileInMaintenance() && !$user->isSuperAdmin()) {
            return [
                'status' => false,
                'code' => 503,
                'maintenance' => true,
                'message' => 'Layanan aplikasi mobile sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan mengakses saat ini.'
            ];
        }

        if ($user->isBanned() || $user->isSuspended() || $user->status === 'nonaktif') {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami atau akun tidak dapat diakses.'
            ];
        }

        if (!in_array($user->level, [UserRole::SUPERADMIN->value, 'Superadmin', UserRole::ADMIN_INSTANSI->value]) && $user->instansi_id && (is_null($user->id_divisi) || !$user->divisi)) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Akun Anda tidak memiliki divisi/unit aktif atau divisi sebelumnya telah dihapus oleh administrator. Silakan hubungi Admin Institusi/Instansi Anda.'
            ];
        }

        $user->tokens()->delete();
        $token = $user->createToken($deviceName . '-' . $user->id)->plainTextToken;

        AuditLogger::auth(
            'mobile_login_password',
            "Pengguna {$user->name} ({$user->email}) berhasil login ke Mobile App via kata sandi.",
            ['platform' => 'mobile', 'role' => $user->level, 'instansi_id' => $user->instansi_id]
        );

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => $user->load('instansi', 'divisi'),
        ];
    }

    public function handleGoogleLoginApi(string $email, string $name, string $googleId, ?string $avatar = null, ?string $idToken = null): array
    {
        // Keamanan: Verifikasi Google ID Token asli jika disertakan
        if (!empty($idToken)) {
            try {
                $googleVerify = Http::timeout(5)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($googleVerify->successful()) {
                    $payload = $googleVerify->json();
                    if (!empty($payload['email'])) {
                        $email = strtolower(trim($payload['email']));
                        $googleId = $payload['sub'] ?? $googleId;
                        if (!empty($payload['name'])) $name = $payload['name'];
                        if (!empty($payload['picture'])) $avatar = $payload['picture'];
                    }
                } else {
                    return [
                        'status' => false,
                        'code' => 401,
                        'message' => 'Token autentikasi Google tidak valid atau telah kedaluwarsa.',
                    ];
                }
            } catch (\Throwable $e) {
                if (app()->environment('production')) {
                    return [
                        'status' => false,
                        'code' => 503,
                        'message' => 'Gagal memverifikasi identitas token Google ke server pusat.',
                    ];
                }
            }
        } elseif (app()->environment('production')) {
            return [
                'status' => false,
                'code' => 422,
                'message' => 'Token autentikasi Google (id_token) wajib disertakan untuk verifikasi keamanan pada server produksi.',
            ];
        }

        $user = $this->authRepository->findByGoogleIdOrEmail($googleId, $email);

        if (!$user) {
            return [
                'status' => false,
                'code' => 404,
                'message' => "Login Google gagal: Akun dengan email ({$email}) belum terdaftar di sistem. Akun hanya dapat dibuat oleh Superadmin atau Admin Instansi/Institusi Anda."
            ];
        }

        $user = $this->authRepository->updateGoogleInfo($user, $googleId, $avatar);

        if ($user->isBanned() || $user->isSuspended() || $user->status === 'nonaktif') {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Akun Anda tidak dapat diakses atau telah dinonaktifkan.'
            ];
        }

        if (MaintenanceMode::isMobileInMaintenance() && !$user->isSuperAdmin()) {
            return [
                'status' => false,
                'code' => 503,
                'maintenance' => true,
                'message' => 'Layanan aplikasi mobile sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan mengakses saat ini.'
            ];
        }

        if (!in_array($user->level, [UserRole::SUPERADMIN->value, 'Superadmin', UserRole::ADMIN_INSTANSI->value]) && $user->instansi_id && (is_null($user->id_divisi) || !$user->divisi)) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'Akun Anda tidak memiliki divisi/unit aktif atau divisi sebelumnya telah dihapus oleh administrator. Silakan hubungi Admin Institusi/Instansi Anda.'
            ];
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile_token-' . $user->id)->plainTextToken;

        AuditLogger::auth(
            'mobile_login_google',
            "Pengguna {$user->name} ({$user->email}) berhasil login ke Mobile App via Google Sign-In.",
            ['platform' => 'mobile', 'role' => $user->level, 'instansi_id' => $user->instansi_id]
        );

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Login Google berhasil!',
            'token' => $token,
            'user' => $user->load('instansi', 'divisi'),
        ];
    }

    public function linkInstansi(User $user, string $code): array
    {
        $instansi = $this->authRepository->findInstansiByInviteCodeOrNpsn($code);

        if (!$instansi) {
            return [
                'success' => false,
                'message' => "Kode undangan atau NPSN '{$code}' tidak ditemukan."
            ];
        }

        $totalActiveQuota = $instansi->getTotalActiveQuota();
        $currentUsers = $this->authRepository->countUsersInInstansi($instansi->id);
        if ($currentUsers >= $totalActiveQuota) {
            return [
                'success' => false,
                'message' => "Kuota anggota pada instansi {$instansi->nama_instansi} ({$totalActiveQuota} slot) sudah mencapai batas maksimal."
            ];
        }

        $this->authRepository->linkUserToInstansi($user, $instansi->id);

        AuditLogger::auth(
            'link_instansi',
            "Pengguna {$user->name} menautkan akun ke instansi {$instansi->nama_instansi}.",
            ['instansi_id' => $instansi->id]
        );

        return [
            'success' => true,
            'message' => "Selamat! Akun Anda berhasil ditautkan ke {$instansi->nama_instansi}.",
            'instansi' => $instansi,
        ];
    }

    public function handleGoogleLoginWeb(string $googleId, string $name, string $email, ?string $avatar = null): array
    {
        if (empty($email)) {
            return [
                'success' => false,
                'message' => 'Alamat email akun Google tidak ditemukan.',
            ];
        }

        $user = $this->authRepository->findByGoogleIdOrEmail($googleId, $email);

        if (!$user) {
            return [
                'success' => false,
                'message' => "Login Google gagal: Akun dengan email ({$email}) belum terdaftar di sistem. Akun hanya dapat dibuat oleh Superadmin atau Admin Institusi/Instansi Anda.",
            ];
        }

        $user = $this->authRepository->updateGoogleInfo($user, $googleId, $avatar);

        if ($user->isBanned()) {
            return ['success' => false, 'message' => 'Akun Anda telah di-banned.'];
        }
        if ($user->isSuspended()) {
            return ['success' => false, 'message' => 'Akun Anda sedang disuspend.'];
        }

        if (!in_array($user->level, [UserRole::SUPERADMIN->value, 'Superadmin', UserRole::ADMIN_INSTANSI->value]) && $user->instansi_id && (is_null($user->id_divisi) || !$user->divisi)) {
            return ['success' => false, 'message' => 'Akun Anda tidak memiliki divisi/unit aktif atau divisi sebelumnya telah dihapus oleh administrator. Silakan hubungi Admin Institusi/Instansi Anda.'];
        }

        if (MaintenanceMode::isWebInMaintenance() && !$user->isSuperAdmin()) {
            return ['success' => false, 'message' => 'Website sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan masuk saat ini.'];
        }

        Auth::login($user, true);

        AuditLogger::auth(
            'web_login_google',
            "Pengguna {$user->name} ({$user->email}) berhasil login via Single Sign-On (SSO) Google Web.",
            ['role' => $user->level, 'instansi_id' => $user->instansi_id, 'google_id' => $googleId]
        );

        return ['success' => true, 'user' => $user];
    }

    public function logoutWeb(Request $request): void
    {
        $user = Auth::user();
        if ($user) {
            AuditLogger::auth(
                'web_logout',
                "Pengguna {$user->name} ({$user->email}) mengakhiri sesi dan logout dari sistem web.",
                ['role' => $user->level, 'instansi_id' => $user->instansi_id]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function logoutApi(User $user): void
    {
        AuditLogger::auth(
            'mobile_logout',
            "Pengguna {$user->name} ({$user->email}) logout dari Mobile App.",
            ['role' => $user->level, 'instansi_id' => $user->instansi_id]
        );

        $user->tokens()->delete();
    }
}
