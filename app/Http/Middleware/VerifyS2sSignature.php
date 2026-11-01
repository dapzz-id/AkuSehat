<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Security\S2sSignatureService;
use App\Services\AuditLogger;

/**
 * Middleware Keamanan Kriptografi Machine-to-Machine (S2S / M2M).
 *
 * Memvalidasi header:
 * - X-Timestamp : UNIX Epoch (detik)
 * - X-Nonce     : Unique One-Time Token (Anti-Replay)
 * - X-Signature : HMAC-SHA256(METHOD + "\n" + PATH + "\n" + TIMESTAMP + "\n" + NONCE + "\n" + SHA256(BODY))
 *
 * Serta menandatangani setiap response keluar dengan X-Server-Timestamp & X-Server-Signature.
 */
class VerifyS2sSignature
{
    public function __construct(
        protected S2sSignatureService $s2sService
    ) {}

    public function handle(Request $request, Closure $next, string $mode = 'adaptive'): Response
    {
        $isStrict = $mode === 'strict' || (bool) config('services.s2s.strict_mode', false);
        $hasSignatureHeaders = $request->hasHeader('X-Signature')
            || $request->hasHeader('X-Timestamp')
            || $request->hasHeader('X-Nonce');

        if ($isStrict || $hasSignatureHeaders) {
            $verification = $this->s2sService->verifyRequest(
                $request->method(),
                $request->path(),
                $request->header('X-Timestamp'),
                $request->header('X-Nonce'),
                (string) $request->getContent(),
                $request->header('X-Signature')
            );

            if (!$verification['valid']) {
                try {
                    AuditLogger::log(
                        's2s_signature_rejected',
                        'security',
                        'Percobaan akses S2S ditolak oleh verifikasi kriptografi HMAC-SHA256.',
                        [
                            'reason' => $verification['reason'],
                            'ip' => $request->ip(),
                            'path' => $request->path(),
                            'method' => $request->method(),
                        ]
                    );
                } catch (\Throwable $e) {
                    // Jangan gagalkan response jika logging gagal
                }

                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Data atau halaman tidak ditemukan.',
                ], 404);
            }
        }

        /** @var Response $response */
        $response = $next($request);

        // Tandatangani response dari server (Server Authenticity Header)
        if ($request->is('api/*') || $request->expectsJson()) {
            $serverTimestamp = time();
            $serverSignature = $this->s2sService->signResponse(
                $response->getStatusCode(),
                $serverTimestamp,
                (string) $response->getContent()
            );

            $response->headers->set('X-Server-Timestamp', (string) $serverTimestamp);
            $response->headers->set('X-Server-Signature', $serverSignature);
        }

        return $response;
    }
}
