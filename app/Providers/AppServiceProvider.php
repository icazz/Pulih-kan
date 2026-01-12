<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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

    public function boot(): void
    {
        // PENTING: Beritahu Laravel untuk mencari manifest di dalam folder .vite
        // Ini adalah standar baru untuk Vite versi 5 ke atas
        Vite::useManifestFilename('.vite/manifest.json');
        
        // Opsional: kamu bisa aktifkan kembali prefetch jika ingin performa lebih cepat
        // Vite::prefetch(concurrency: 3);
    }
}
