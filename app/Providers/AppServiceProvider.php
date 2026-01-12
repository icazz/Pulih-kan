<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite; // <--- JANGAN LUPA BARIS INI
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
        // KITA UBAH KE SINI:
        // Karena di screenshot kamu filenya ada di "public/build/manifest.json"
        // Kita suruh Laravel pakai nama file itu saja, tanpa embel-embel ".vite"
        Vite::useManifestFilename('manifest.json');
    }
}