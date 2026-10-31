<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     * Menerapkan HTTP Security Headers untuk mencegah MIME-sniffing, Clickjacking, dan XSS.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // 1. Mencegah MIME-type sniffing (Mencegah browser mengeksekusi file upload sebagai script)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 2. Mencegah Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 3. XSS Filter bawaan browser
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Hardware & Feature Permissions Policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 6. HTTP Strict Transport Security (HSTS) jika koneksi aman atau di environment produksi
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
