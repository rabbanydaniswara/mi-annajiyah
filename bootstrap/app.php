<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsurePpdbOpen;
use App\Http\Middleware\EnsureSingleActiveAdminSession;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', ''))
        )));

        if ($trustedProxies !== []) {
            $middleware->trustProxies(at: $trustedProxies === ['*'] ? '*' : $trustedProxies);
        }

        $middleware->append(SecurityHeaders::class);
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'ppdb.open' => EnsurePpdbOpen::class,
            'single.admin.session' => EnsureSingleActiveAdminSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
