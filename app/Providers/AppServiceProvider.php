<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::useManifestFilename('manifest.json');

        // 2. Fix Mixed Content (TAMBAHAN BARU)
        // Jika aplikasi berjalan di Production (Railway), paksa gunakan HTTPS
       
        if (str_starts_with(config('app.url'), 'https://')) {
        URL::forceScheme('https');
    }
        if($this->app->environment('production') || $this->app->environment('development')) {
        URL::forceScheme('https');
        }
    }
}