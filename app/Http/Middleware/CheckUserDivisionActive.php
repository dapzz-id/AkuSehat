<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserDivisionActive
{
    /**
     * Handle an incoming request.
     *
     * Memastikan user (terutama Member) terikat ke divisi yang aktif.
     * Jika divisi user bernilai NULL (misal divisinya telah dihapus oleh admin),
     * maka user otomatis di-logout dari website dan mobile app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Pengecekan berlaku khusus untuk Member (siswa/anggota) yang terikat instansi
        if ($user && $user->isMember() && $user->instansi_id) {
            // Jika id_divisi NULL atau relasi divisi tidak ditemukan lagi di database
            if (is_null($user->id_divisi) || !$user->divisi) {
                // Skenario 1: Request Mobile App / API (Sanctum / JSON)
                if ($request->expectsJson() || $request->is('api/*')) {
                    // Revoke token saat ini atau semua token user
                    if ($request->user() && $request->user()->currentAccessToken()) {
                        $request->user()->currentAccessToken()->delete();
                    } else {
                        $user->tokens()->delete();
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'Divisi/Kelas Anda telah dihapus oleh administrator. Akun Anda otomatis dikeluarkan dari sistem.',
                        'auto_logout' => true,
                    ], 401);
                }

                // Skenario 2: Request Website (Web Session)
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Divisi/Kelas akun Anda telah dihapus oleh administrator. Anda telah otomatis dikeluarkan dari sistem. Silakan hubungi admin institusi Anda.');
            }
        }

        return $next($request);
    }
}
