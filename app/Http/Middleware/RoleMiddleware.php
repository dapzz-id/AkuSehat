<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect('/login');
        }

        $userLevel = $user->level;

        // Flatten allowed roles (e.g. role:Admin,Admin Instansi or role:Admin)
        $allowedRoles = [];
        foreach ($roles as $r) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $r));
        }
        $allowedRoles = array_map('trim', $allowedRoles);

        // SuperAdmin memiliki wewenang akses penuh
        if ($userLevel === 'SuperAdmin') {
            return $next($request);
        }

        // Jika rute meminta 'Admin', user dengan level 'Admin Instansi' juga diperbolehkan
        if (in_array('Admin', $allowedRoles) && $userLevel === 'Admin Instansi') {
            return $next($request);
        }

        if (!in_array($userLevel, $allowedRoles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses tidak diizinkan untuk peran ' . $userLevel
                ], 403);
            }
            abort(403, 'Akses tidak diizinkan untuk peran ' . $userLevel);
        }

        return $next($request);
    }
}
