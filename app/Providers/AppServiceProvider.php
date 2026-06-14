<?php

namespace App\Providers;

use App\Helpers\PpdbHelper;
use App\Helpers\PublicCacheHelper;
use App\Models\KontenWeb;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS only for the configured public host so local HTTP previews still load assets correctly.
        $appUrl = config('app.url');
        $appHost = parse_url($appUrl, PHP_URL_HOST);

        if (str_starts_with($appUrl, 'https://') && $appHost && request()->getHost() === $appHost) {
            URL::forceScheme('https');
        }

        // Use Tailwind CSS pagination styling
        Paginator::useTailwind();

        // Rate Limiters
        RateLimiter::for('pendaftaran', function ($request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('cek-pendaftaran', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('admin-login', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Share contact info to all public views
        View::composer(['layouts.public', 'public.*'], function ($view) {
            $konten = collect(Cache::remember(PublicCacheHelper::KONTEN_WEB, now()->addHours(6), function () {
                return KontenWeb::orderBy('urutan')->pluck('konten', 'tipe')->all();
            }));

            $view->with([
                'kontenWeb' => $konten,
                'ppdbSettings' => PpdbHelper::settingsFrom($konten),
            ]);
        });
    }
}
