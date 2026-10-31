<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;

class EnforceTenantAccess
{
    /**
     * Handle an incoming request.
     * Memastikan akses dibatasi secara ketat hanya pada instansi/sekolah yang bersangkutan.
     * Sekolah A tidak dapat mengakses, melihat, mengubah, atau menghapus data Sekolah B.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Lewati jika belum login (dikelola oleh middleware auth)
        if (!Auth::check()) {
            return $next($request);
        }

        $currentUser = Auth::user();

        // 2. SuperAdmin diizinkan mengakses semua modul
        if ($currentUser->isSuperAdmin()) {
            return $next($request);
        }

        // 3. Blokir akses ke rute /superadmin untuk non-SuperAdmin
        if ($request->is('superadmin*')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Halaman SuperAdmin hanya untuk Administrator Pusat.'
                ], 403);
            }
            abort(403, 'Akses Ditolak: Halaman SuperAdmin hanya untuk Administrator Pusat.');
        }

        $currentInstansiId = $currentUser->instansi_id;

        // 4. Pengguna tanpa instansi_id tidak boleh mengakses modul instansi
        if (!$currentInstansiId) {
            if ($request->is('admin*') || $request->is('api/member*') || $request->is('api/health*')) {
                if ($request->routeIs('link.instansi') || $request->is('link-instansi*') || $request->is('profile*') || $request->is('logout')) {
                    return $next($request);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Anda belum terhubung dengan institusi/instansi manapun. Silakan tautkan kode undangan instansi terlebih dahulu.'
                    ], 403);
                }

                return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung dengan institusi/instansi manapun.');
            }
            return $next($request);
        }

        // 5. Validasi Route Parameters (IDOR Prevention)
        if ($request->route()) {
            foreach ($request->route()->parameters() as $paramKey => $paramValue) {
            // A. Parameter User ({user})
            if ($paramValue instanceof User) {
                if ($paramValue->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Data pengguna ini milik institusi/instansi lain.');
                }
            } elseif ($paramKey === 'user' && is_string($paramValue)) {
                $targetUser = User::withoutGlobalScopes()->find($paramValue);
                if ($targetUser && $targetUser->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Data pengguna ini milik institusi/instansi lain.');
                }
            }

            // B. Parameter Divisi ({divisi})
            if ($paramValue instanceof Divisi) {
                if ($paramValue->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Divisi ini milik institusi/instansi lain.');
                }
            } elseif ($paramKey === 'divisi' && is_string($paramValue)) {
                $targetDivisi = Divisi::withoutGlobalScopes()->find($paramValue);
                if ($targetDivisi && $targetDivisi->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Divisi ini milik institusi/instansi lain.');
                }
            }

            // C. Parameter Instansi ({instansi})
            if ($paramValue instanceof Instansi) {
                if ($paramValue->id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Anda tidak memiliki akses ke institusi/instansi lain.');
                }
            } elseif ($paramKey === 'instansi' && is_string($paramValue)) {
                if ($paramValue !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Anda tidak memiliki akses ke institusi/instansi lain.');
                }
            }

            // D. Model lain yang memiliki atribut instansi_id atau user
            if (is_object($paramValue) && method_exists($paramValue, 'getAttribute')) {
                if ($paramValue->instansi_id && $paramValue->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Data ini milik institusi/instansi lain.');
                }
                if ($paramValue->user && $paramValue->user->instansi_id && $paramValue->user->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Data ini milik institusi/instansi lain.');
                }
            }
        }
    }

        // 6. Validasi Request Body (Prevent Cross-Tenant Tampering)
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            // A. Jika request mengirim instansi_id, pastikan sama dengan instansi_id pengguna saat ini
            if ($request->has('instansi_id') && $request->filled('instansi_id')) {
                if ($request->input('instansi_id') !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Anda tidak dapat menetapkan instansi_id ke instansi/sekolah lain.');
                }
            }

            // B. Jika request mengirim id_divisi / divisi, pastikan divisi tersebut milik instansi pengguna
            $divisiInput = $request->input('id_divisi') ?: $request->input('new_divisi');
            if ($divisiInput && $divisiInput !== 'none' && $divisiInput !== 'alumni') {
                $checkDivisi = Divisi::withoutGlobalScopes()->find($divisiInput);
                if ($checkDivisi && $checkDivisi->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Divisi yang dipilih milik instansi/sekolah lain.');
                }
            }

            // C. Jika request mengirim id_user / user_id, pastikan user target milik instansi pengguna
            $userInput = $request->input('id_user') ?: $request->input('user_id');
            if ($userInput) {
                $checkUser = User::withoutGlobalScopes()->find($userInput);
                if ($checkUser && $checkUser->instansi_id !== $currentInstansiId) {
                    return $this->forbiddenResponse($request, 'Akses Ditolak: Pengguna yang dipilih milik instansi/sekolah lain.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Respon saat terjadi pelanggaran batasan tenant.
     * Menggunakan HTTP 404 Not Found (Anti-IDOR) agar penyerang tidak dapat melakukan probing/penebakan eksistensi data ID.
     */
    protected function forbiddenResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        abort(404, 'Data tidak ditemukan.');
    }
}
