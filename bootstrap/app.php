<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\RouteServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
            'PDF' => Barryvdh\DomPDF\Facade::class,
            'check.division' => \App\Http\Middleware\CheckUserDivisionActive::class,
            'tenant.isolate' => \App\Http\Middleware\EnforceTenantAccess::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckUserDivisionActive::class,
            \App\Http\Middleware\EnforceTenantAccess::class,
            \App\Http\Middleware\CheckMaintenance::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\CheckUserDivisionActive::class,
            \App\Http\Middleware\EnforceTenantAccess::class,
            \App\Http\Middleware\CheckMaintenance::class,
        ]);

        $middleware->use([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

