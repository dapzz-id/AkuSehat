<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckLicenseActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user() ?: auth()->user();

        // 1. SuperAdmin bebas blokir lisensi
        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        // 2. Rute otentikasi & keluar selalu diizinkan
        if ($request->routeIs('logout') || $request->is('logout')) {
            return $next($request);
        }

        if (!Schema::hasTable('instansi') || !Schema::hasTable('license_keys')) {
            return $next($request);
        }

        if ($user->instansi_id) {
            $instansi = $user->instansi;
            $hasActiveLicense = $instansi ? $instansi->hasActiveLicense() : false;

            // Jika tidak ada lisensi aktif (atau seluruh paket telah kedaluwarsa)
            if (!$hasActiveLicense) {
                // Sesuai requirement: Jika lisensi habis, Admin Instansi/Institusi tetap bisa login
                // namun HANYA bisa mengakses fitur Billing & Manajemen User saja (serta Profile).
                if ($user->isAdminInstansi()) {
                    if (
                        $request->is('admin/billing*') ||
                        $request->is('admin/users*') ||
                        $request->is('profile*')
                    ) {
                        return $next($request);
                    }

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Masa aktif lisensi institusi Anda telah berakhir. Anda hanya dapat mengakses menu Billing dan Manajemen User.',
                        ], 403);
                    }

                    return redirect()->route('admin.billing.index')
                        ->with('warning', 'Masa aktif lisensi institusi Anda telah berakhir. Anda hanya dapat mengakses menu Billing dan Manajemen User untuk memperpanjang lisensi atau mengelola akun.');
                }

                // Untuk Member / Siswa jika lisensi habis:
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Masa aktif lisensi institusi/instansi Anda telah berakhir. Silakan hubungi Admin Institusi Anda untuk perpanjangan lisensi.',
                    ], 403);
                }

                abort(403, 'Akses Diblokir: Masa aktif lisensi institusi/instansi Anda telah berakhir. Kuota sistem kembali ke default 1 (Khusus Admin Instansi). Silakan hubungi Admin Institusi/Instansi Anda untuk perpanjangan lisensi.');
            }
        }

        return $next($request);
    }
}
