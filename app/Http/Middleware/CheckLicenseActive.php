<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $user = auth()->user();

        // SuperAdmin bebas blokir lisensi
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user && $user->instansi_id) {
            $instansi = $user->instansi;
            $hasActiveLicense = $instansi ? $instansi->hasActiveLicense() : false;

            // Jika tidak ada lisensi aktif (atau seluruh paket telah kedaluwarsa)
            if (!$hasActiveLicense) {
                // Sesuai requirement: Jika tidak diperpanjang, kuota kembali ke default 1
                // (khusus akun Admin Instansi/Institusi saja) agar tetap bisa login dan mengelola billing/sistem.
                if ($user->isAdminInstansi()) {
                    return $next($request);
                }

                if ($request->routeIs('logout')) {
                    return $next($request);
                }

                abort(403, 'Akses Diblokir: Masa aktif lisensi institusi/instansi Anda telah berakhir. Kuota sistem kembali ke default 1 (Khusus Admin Instansi). Silakan hubungi Admin Institusi/Instansi Anda untuk perpanjangan lisensi.');
            }
        }

        return $next($request);
    }
}
