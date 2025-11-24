<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Auth;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah mode maintenance aktif
        if (app()->isDownForMaintenance()) {
            $user = Auth::user();

            // ✅ Lewatkan jika SuperAdmin atau route login/logout/superadmin/*
            if (
                ($user && $user->level == 'SuperAdmin') ||
                $request->is('login') ||
                $request->is('logout') ||
                $request->is('superadmin/*') ||
                $request->is('api/*') ||
                $request->is('dashboard')
            ) {
                return $next($request);
            }

            // ❌ Selain itu tampilkan halaman 503 maintenance
            throw new HttpException(503, 'Website sedang dalam pemeliharaan.');
        }

        return $next($request);
    }
}
