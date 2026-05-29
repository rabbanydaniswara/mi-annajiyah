<?php

namespace App\Providers;

use App\Helpers\PublicCacheHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;

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

        \Illuminate\Support\Facades\RateLimiter::for('cek-pendaftaran', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin-login', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Share contact info to all public views
        \Illuminate\Support\Facades\View::composer(['layouts.public', 'public.*'], function ($view) {
            $konten = collect(Cache::remember(PublicCacheHelper::KONTEN_WEB, now()->addHours(6), function () {
                return \App\Models\KontenWeb::orderBy('urutan')->pluck('konten', 'tipe')->all();
            }));

            $view->with('kontenWeb', $konten);
        });
    }
}
