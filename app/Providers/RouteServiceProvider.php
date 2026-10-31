<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $identifier = \Illuminate\Support\Str::lower($request->input('username', $request->input('email', 'guest')));
            return Limit::perMinute(5)
                ->by($identifier . '|' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json([
                            'status' => false,
                            'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                        ], 429)->withHeaders($headers);
                    }
                    return back()->withErrors([
                        'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                    ])->withHeaders($headers);
                });
        });
    }
}