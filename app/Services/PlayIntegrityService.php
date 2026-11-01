<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlayIntegrityService
{
    protected string $packageName;
    protected ?string $serviceAccountJson;

    public function __construct()
    {
        $this->packageName = config('services.google_play.package_name', 'neuracakrawira.asia.akusehat');
        $this->serviceAccountJson = config('services.google_play.service_account_json');
    }

    /**
     * Memverifikasi token Play Integrity dari aplikasi mobile Android
     */
    public function verifyToken(string $integrityToken): array
    {
        if (empty($integrityToken)) {
            return [
                'success' => false,
                'message' => 'Token integritas tidak boleh kosong.',
            ];
        }

        $accessToken = $this->getGoogleAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Gagal mendapatkan otorisasi Google API Service Account.',
            ];
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://playintegrity.googleapis.com/v1/{$this->packageName}:decodeIntegrityToken", [
                    'integrityToken' => $integrityToken,
                ]);

            if (!$response->successful()) {
                Log::warning('Play Integrity decode failed: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal mendekripsi token Play Integrity dari Google.',
                    'details' => $response->json(),
                ];
            }

            $payload = $response->json()['tokenPayloadExternal'] ?? [];
            return $this->evaluateVerdicts($payload);
        } catch (\Exception $e) {
            Log::error('Play Integrity error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat verifikasi Play Integrity: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Evaluasi verdict dari Google Play Integrity
     */
    protected function evaluateVerdicts(array $payload): array
    {
        $appIntegrity = $payload['appIntegrity'] ?? [];
        $deviceIntegrity = $payload['deviceIntegrity'] ?? [];
        $accountDetails = $payload['accountDetails'] ?? [];
        $environmentDetails = $payload['environmentDetails'] ?? [];

        // 1. Validasi App Recognition (Biner resmi dari Play Store)
        $appRecognitionVerdict = $appIntegrity['appRecognitionVerdict'] ?? '';
        $isRecognizedApp = in_array($appRecognitionVerdict, ['PLAY_RECOGNIZED', 'UNEVALUATED']);

        // 2. Validasi Integritas Perangkat (Bukan emulator modifikasi berbahaya / rooted tanpa proteksi)
        $deviceVerdicts = $deviceIntegrity['deviceRecognitionVerdict'] ?? [];
        $meetsIntegrity = in_array('MEETS_DEVICE_INTEGRITY', $deviceVerdicts) || 
                          in_array('MEETS_BASIC_INTEGRITY', $deviceVerdicts) || 
                          in_array('MEETS_STRONG_INTEGRITY', $deviceVerdicts);

        // 3. Validasi Play Protect
        $playProtectVerdict = $environmentDetails['playProtectVerdict'] ?? 'NO_ISSUES';
        $isPlayProtectSafe = in_array($playProtectVerdict, ['NO_ISSUES', 'NO_DATA']);

        $isValid = $isRecognizedApp && $meetsIntegrity && $isPlayProtectSafe;

        return [
            'success' => $isValid,
            'is_genuine_device' => $meetsIntegrity,
            'is_genuine_app' => $isRecognizedApp,
            'play_protect_safe' => $isPlayProtectSafe,
            'licensing' => $accountDetails['appLicensingVerdict'] ?? 'UNKNOWN',
            'verdicts' => [
                'device' => $deviceVerdicts,
                'app' => $appRecognitionVerdict,
                'play_protect' => $playProtectVerdict,
            ],
            'raw' => $payload,
        ];
    }

    /**
     * Dapatkan Google OAuth Access Token via Service Account JWT
     */
    protected function getGoogleAccessToken(): ?string
    {
        if (empty($this->serviceAccountJson)) {
            Log::warning('GOOGLE_PLAY_SERVICE_ACCOUNT_JSON belum dikonfigurasi di .env');
            return null;
        }

        try {
            $credentials = json_decode($this->serviceAccountJson, true);
            if (!isset($credentials['client_email']) || !isset($credentials['private_key'])) {
                if (file_exists($this->serviceAccountJson)) {
                    $credentials = json_decode(file_get_contents($this->serviceAccountJson), true);
                }
            }

            if (!isset($credentials['client_email']) || !isset($credentials['private_key'])) {
                return null;
            }

            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = base64_encode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/playintegrity',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signingInput = "{$header}.{$claim}";
            openssl_sign($signingInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = $signingInput . '.' . base64_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get Google Access Token for Play Integrity: ' . $e->getMessage());
            return null;
        }
    }
}
