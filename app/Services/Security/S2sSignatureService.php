<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;

/**
 * Machine-to-Machine (M2M / S2S) Cryptographic Request Signing & Anti-Replay Service.
 *
 * Menggunakan skema kanonikal setara AWS Signature v4 / Stripe Webhook:
 *   CanonicalPayload = UPPER(METHOD) + "\n" + "/" + TRIM(PATH, "/") + "\n" + TIMESTAMP + "\n" + NONCE + "\n" + SHA256(RAW_BODY)
 *   Signature        = HMAC-SHA256(CanonicalPayload, SharedSecret)
 *
 * Dilengkapi dengan:
 * 1. Time-Window Drift Tolerance (±60 detik) untuk menahan paket usang.
 * 2. One-Time Nonce Cache Lock untuk memblokir Replay Attack 100%.
 * 3. Constant-Time Comparison (hash_equals) untuk mencegah Side-Channel Timing Attack.
 */
class S2sSignatureService
{
    public const DEFAULT_TOLERANCE_SECONDS = 60;

    /**
     * Ambil kunci rahasia S2S (Shared Secret) atau turunkan secara kriptografis (HKDF-SHA256) dari APP_KEY.
     */
    public function getSharedSecret(): string
    {
        $configured = config('services.s2s.secret') ?: env('S2S_SHARED_SECRET');
        if (!empty($configured)) {
            return (string) $configured;
        }

        $appKey = (string) config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        return bin2hex(hash_hkdf('sha256', $appKey, 32, 'AkuSehat-S2S-HMAC-v1'));
    }

    /**
     * Bangun canonical payload string yang akan ditandatangani.
     */
    public function buildCanonicalString(
        string $method,
        string $path,
        int|string $timestamp,
        string $nonce,
        string $rawBody = ''
    ): string {
        $normalizedMethod = strtoupper(trim($method));
        $normalizedPath = '/' . ltrim(trim($path), '/');
        if (in_array($normalizedMethod, ['GET', 'HEAD'], true)) {
            $rawBody = '';
        }
        $bodyHash = hash('sha256', $rawBody);

        return implode("\n", [
            $normalizedMethod,
            $normalizedPath,
            (string) $timestamp,
            trim($nonce),
            $bodyHash,
        ]);
    }

    /**
     * Buat tanda tangan kriptografi HMAC-SHA256 untuk request keluar/masuk.
     */
    public function signRequest(
        string $method,
        string $path,
        int|string $timestamp,
        string $nonce,
        string $rawBody = '',
        ?string $customSecret = null
    ): string {
        $canonical = $this->buildCanonicalString($method, $path, $timestamp, $nonce, $rawBody);
        $secret = $customSecret ?? $this->getSharedSecret();

        return hash_hmac('sha256', $canonical, $secret);
    }

    /**
     * Verifikasi tanda tangan request S2S beserta Timestamp & Anti-Replay Nonce.
     *
     * @return array{valid: bool, reason: string|null}
     */
    public function verifyRequest(
        string $method,
        string $path,
        int|string|null $timestamp,
        ?string $nonce,
        string $rawBody,
        ?string $providedSignature,
        ?string $customSecret = null
    ): array {
        if (empty($timestamp) || !is_numeric($timestamp) || empty($nonce) || empty($providedSignature)) {
            return ['valid' => false, 'reason' => 'missing_headers'];
        }

        $nonce = trim($nonce);
        if (strlen($nonce) < 8 || strlen($nonce) > 128) {
            return ['valid' => false, 'reason' => 'invalid_nonce_length'];
        }

        $tolerance = (int) config('services.s2s.tolerance_seconds', self::DEFAULT_TOLERANCE_SECONDS);
        $now = time();
        $reqTime = (int) $timestamp;

        // 1. Cek batas waktu (Time-Window Anti-Replay)
        if (abs($now - $reqTime) > $tolerance) {
            return ['valid' => false, 'reason' => 'timestamp_expired'];
        }

        // 2. Hitung ekspektasi HMAC-SHA256 & bandingkan secara Constant-Time (hash_equals)
        $expectedSignature = $this->signRequest($method, $path, $reqTime, $nonce, $rawBody, $customSecret);
        if (!hash_equals($expectedSignature, strtolower(trim($providedSignature)))) {
            return ['valid' => false, 'reason' => 'signature_mismatch'];
        }

        // 3. Kunci Nonce sekali pakai di Cache (Atomik: gagal jika Nonce sudah pernah dipakai)
        $nonceCacheKey = 's2s_nonce:' . hash('sha256', $nonce . ':' . $reqTime);
        try {
            $stored = Cache::add($nonceCacheKey, $now, $tolerance * 2);
        } catch (\Throwable $e) {
            $stored = Cache::store('file')->add($nonceCacheKey, $now, $tolerance * 2);
        }
        if (!$stored) {
            return ['valid' => false, 'reason' => 'replay_detected'];
        }

        return ['valid' => true, 'reason' => null];
    }

    /**
     * Buat tanda tangan kriptografi untuk response dari server (Server Authenticity Proof).
     */
    public function signResponse(int $statusCode, int|string $timestamp, string $rawBody, ?string $customSecret = null): string
    {
        $bodyHash = hash('sha256', $rawBody);
        $canonical = implode("\n", [
            (string) $statusCode,
            (string) $timestamp,
            $bodyHash,
        ]);

        return hash_hmac('sha256', $canonical, $customSecret ?? $this->getSharedSecret());
    }
}
