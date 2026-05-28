<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS when running behind Cloudflare Tunnel
        if (str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Use Tailwind CSS pagination styling
        Paginator::useTailwind();

        // Rate Limiters
        \Illuminate\Support\Facades\RateLimiter::for('pendaftaran', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin-login', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Share contact info to all public views
        \Illuminate\Support\Facades\View::composer(['layouts.public', 'public.*'], function ($view) {
            $konten = \App\Models\KontenWeb::all()->pluck('konten', 'tipe');
            $view->with('kontenWeb', $konten);
        });
    }
}
