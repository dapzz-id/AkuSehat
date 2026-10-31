<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MaintenanceMode;
use Illuminate\Support\Facades\Auth;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Bypass asset static & build assets
        if (
            $request->is('build/*') || 
            $request->is('src/*') || 
            $request->is('favicon.ico') ||
            $request->is('robots.txt') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // 2. Cek apakah request ke API (Mobile)
        if ($request->is('api/*')) {
            // Bypass route cek update/rilis dan sinkronisasi realtime agar mobile dapat mendeteksi status secara realtime
            if (
                $request->is('api/app-update/*') ||
                $request->is('api/system/realtime-sync*') ||
                $request->is('api/me/sync*') ||
                $request->is('api/me/modules*')
            ) {
                return $next($request);
            }

            // Jika Mobile Maintenance aktif
            if (MaintenanceMode::isMobileInMaintenance()) {
                // Resolusi user dari Sanctum token atau guard default
                $user = auth('sanctum')->user() ?? Auth::user();

                // ✅ Hanya lolos jika user terautentikasi dan merupakan SuperAdmin
                if ($user && $user->isSuperAdmin()) {
                    return $next($request);
                }

                // Izinkan endpoint login API agar SuperAdmin bisa login melalui mobile
                if ($request->is('api/login') || $request->is('api/auth/google')) {
                    return $next($request);
                }

                return response()->json([
                    'status' => false,
                    'maintenance' => true,
                    'message' => 'Layanan aplikasi mobile sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan mengakses saat ini.',
                ], 503);
            }

            return $next($request);
        }

        // 3. Cek apakah Web Maintenance aktif
        if (MaintenanceMode::isWebInMaintenance()) {
            $user = Auth::user();

            // ✅ Jika user yang login adalah SuperAdmin, izinkan akses penuh tanpa halangan
            if ($user && $user->isSuperAdmin()) {
                return $next($request);
            }

            // Izinkan route login & logout agar SuperAdmin bisa login ke sistem
            if ($request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // Jika bukan SuperAdmin: tampilkan halaman 503 Maintenance
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'maintenance' => true,
                    'message' => 'Website sedang dalam masa pemeliharaan (Maintenance Mode). Hanya Super Administrator yang diizinkan mengakses saat ini.',
                ], 503);
            }

            return response()->view('errors.503', [], 503);
        }

        return $next($request);
    }
}
