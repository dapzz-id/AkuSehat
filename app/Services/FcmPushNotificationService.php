<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FcmPushNotificationService
{
    /**
     * Kirim notifikasi push ke satu pengguna (ke seluruh device aktif miliknya)
     */
    public function sendToUser(User|string $user, string $title, string $body, array $data = []): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $userModel = $user instanceof User ? $user : User::find($userId);

        $tokens = [];

        // 1. Ambil dari kolom fcm_token di users table
        if ($userModel && !empty($userModel->fcm_token)) {
            $tokens[] = $userModel->fcm_token;
        }

        // 2. Ambil dari user_device_tokens table jika ada
        try {
            $deviceTokens = DB::table('user_device_tokens')
                ->where('user_id', $userId)
                ->pluck('token')
                ->toArray();
            $tokens = array_unique(array_merge($tokens, $deviceTokens));
        } catch (\Throwable $e) {
            // Ignore if table not queried
        }

        if (empty($tokens)) {
            return [
                'status' => false,
                'message' => 'Pengguna tidak memiliki token FCM perangkat yang terdaftar.',
                'sent_count' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Kirim notifikasi push ke array token perangkat
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_filter(array_unique($tokens)));
        if (empty($tokens)) {
            return [
                'status' => false,
                'message' => 'Daftar token FCM kosong.',
                'sent_count' => 0,
            ];
        }

        $projectId = trim((string) config('services.fcm.project_id'));
        $serviceAccountConfig = trim((string) config('services.fcm.service_account_json', 'google/firebase-service-account.json'));
        $serverKey = trim((string) config('services.fcm.server_key'));

        // Cek path file service account secara fleksibel
        $realServiceAccountPath = $this->resolveServiceAccountPath($serviceAccountConfig);

        // Prioritas 1 (STANDAR UTAMA): FCM HTTP v1 API dengan Service Account JSON
        if ($realServiceAccountPath && file_exists($realServiceAccountPath)) {
            // Otomatis ekstrak project_id dari file jika tidak diisi di .env
            if (empty($projectId)) {
                $rawKey = @json_decode(file_get_contents($realServiceAccountPath), true);
                $projectId = $rawKey['project_id'] ?? '';
            }

            if (!empty($projectId)) {
                return $this->sendViaFcmV1($projectId, $realServiceAccountPath, $tokens, $title, $body, $data);
            }
        }

        // Prioritas 2 (Fallback): FCM Legacy API jika Server Key tersedia
        if (!empty($serverKey)) {
            return $this->sendViaFcmLegacy($serverKey, $tokens, $title, $body, $data);
        }

        Log::warning('FCM Push Notification: File Service Account JSON FCM v1 belum ditemukan di storage/app/' . $serviceAccountPath, [
            'tokens_count' => count($tokens),
            'title' => $title,
        ]);

        return [
            'status' => false,
            'message' => 'Kredensial FCM v1 belum dikonfigurasi. Letakkan file Service Account JSON di storage/app/firebase-service-account.json.',
            'sent_count' => 0,
        ];
    }

    /**
     * Kirim notifikasi via FCM Legacy HTTP API
     */
    protected function sendViaFcmLegacy(string $serverKey, array $tokens, string $title, string $body, array $data): array
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Pastikan semua data values adalah string (persyaratan FCM Android background)
        $stringData = [];
        foreach ($data as $k => $v) {
            $stringData[$k] = is_array($v) ? json_encode($v) : (string) $v;
        }
        $stringData['title'] = $title;
        $stringData['body'] = $body;
        $stringData['timestamp'] = (string) now()->timestamp;

        $payload = [
            'registration_ids' => $tokens,
            'priority' => 'high',
            'content_available' => true,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'android_channel_id' => 'akusehat_high_importance_channel',
                'icon' => 'ic_notification',
            ],
            'data' => $stringData,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($url, $payload);

            if ($response->successful()) {
                $resData = $response->json();
                $success = $resData['success'] ?? 0;
                $failure = $resData['failure'] ?? 0;

                // Cleanup token tidak valid
                if (!empty($resData['results'])) {
                    $this->cleanupInvalidLegacyTokens($tokens, $resData['results']);
                }

                return [
                    'status' => true,
                    'sent_count' => $success,
                    'failed_count' => $failure,
                    'response' => $resData,
                ];
            }

            Log::error('FCM Legacy Send Error: ' . $response->body());
            return [
                'status' => false,
                'message' => 'FCM API error: ' . $response->status(),
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('FCM Legacy Send Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Gagal menghubungi FCM: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi via FCM HTTP v1 API
     */
    protected function sendViaFcmV1(string $projectId, string $serviceAccountPath, array $tokens, string $title, string $body, array $data): array
    {
        $accessToken = $this->getGoogleAccessToken($serviceAccountPath);
        if (!$accessToken) {
            return [
                'status' => false,
                'message' => 'Gagal mengotentikasi Google Service Account untuk FCM v1 API.',
                'sent_count' => 0,
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $stringData = [];
        foreach ($data as $k => $v) {
            $stringData[$k] = is_array($v) ? json_encode($v) : (string) $v;
        }
        $stringData['title'] = $title;
        $stringData['body'] = $body;

        $sentCount = 0;
        $failCount = 0;

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'akusehat_high_importance_channel',
                            'sound' => 'default',
                            'default_sound' => true,
                            'default_vibrate_timings' => true,
                        ],
                    ],
                    'data' => $stringData,
                ],
            ];

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->timeout(8)->post($url, $payload);

                if ($response->successful()) {
                    $sentCount++;
                } else {
                    $failCount++;
                    $resJson = $response->json();
                    $errorCode = $resJson['error']['details'][0]['errorCode'] ?? null;
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        $this->removeInvalidToken($token);
                    }
                }
            } catch (\Throwable $e) {
                $failCount++;
            }
        }

        return [
            'status' => true,
            'sent_count' => $sentCount,
            'failed_count' => $failCount,
        ];
    }

    /**
     * Resolusi path file service account secara fleksibel
     */
    public function resolveServiceAccountPath(string $inputPath): ?string
    {
        $inputPath = trim($inputPath);
        $candidates = [
            $inputPath,
            base_path($inputPath),
            storage_path('app/' . ltrim($inputPath, '/\\')),
            storage_path('app/google/' . ltrim($inputPath, '/\\')),
            base_path('storage/app/' . ltrim($inputPath, '/\\')),
            storage_path('app/' . preg_replace('#^storage/app/#', '', ltrim($inputPath, '/\\'))),
        ];

        // Jika user memasukkan nama folder (misal: storage/app/google/ atau google/)
        foreach ([$inputPath, base_path($inputPath), storage_path('app/' . ltrim($inputPath, '/\\'))] as $dirCandidate) {
            if (!empty($dirCandidate) && is_dir($dirCandidate)) {
                $candidates[] = rtrim($dirCandidate, '/\\') . '/firebase-service-account.json';
                $foundJsons = glob(rtrim($dirCandidate, '/\\') . '/*.json');
                if ($foundJsons) {
                    $candidates = array_merge($candidates, $foundJsons);
                }
            }
        }

        // Cek juga default fallback: storage/app/google/firebase-service-account.json dan storage/app/firebase-service-account.json
        $candidates[] = storage_path('app/google/firebase-service-account.json');
        $candidates[] = storage_path('app/firebase-service-account.json');

        foreach ($candidates as $cand) {
            if (!empty($cand) && is_file($cand)) {
                return realpath($cand) ?: $cand;
            }
        }

        return null;
    }

    /**
     * Dapatkan OAuth2 Access Token dari Service Account JSON
     */
    protected function getGoogleAccessToken(string $path): ?string
    {
        $realPath = $this->resolveServiceAccountPath($path) ?: $path;
        if (!file_exists($realPath)) {
            Log::error("FCM Service Account file tidak ditemukan di: {$realPath}");
            return null;
        }

        $cacheKey = 'fcm_v1_bearer_' . md5($realPath);
        $cachedToken = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if (!empty($cachedToken)) {
            return $cachedToken;
        }

        try {
            $keyData = json_decode(file_get_contents($realPath), true);
            if (!$keyData || empty($keyData['private_key']) || empty($keyData['client_email'])) {
                return null;
            }

            // Buat JWT token untuk Google OAuth2
            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claim = json_encode([
                'iss' => $keyData['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));

            openssl_sign($base64UrlHeader . '.' . $base64UrlClaim, $signature, $keyData['private_key'], 'sha256');
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = $base64UrlHeader . '.' . $base64UrlClaim . '.' . $base64UrlSignature;

            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($tokenResponse->successful()) {
                $token = $tokenResponse->json()['access_token'] ?? null;
                if ($token) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $token, 3300); // 55 menit
                }
                return $token;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('FCM getGoogleAccessToken Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Hapus token kedaluwarsa dari database
     */
    protected function cleanupInvalidLegacyTokens(array $tokens, array $results): void
    {
        foreach ($results as $index => $result) {
            if (isset($result['error']) && in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                if (isset($tokens[$index])) {
                    $this->removeInvalidToken($tokens[$index]);
                }
            }
        }
    }

    /**
     * Hapus token spesifik dari users & user_device_tokens
     */
    public function removeInvalidToken(string $token): void
    {
        try {
            DB::table('users')->where('fcm_token', $token)->update([
                'fcm_token' => null,
                'fcm_platform' => null,
            ]);
            DB::table('user_device_tokens')->where('token', $token)->delete();
        } catch (\Throwable $e) {
            // Ignore
        }
    }
}

