<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'is_admin' => $request->user()->is_admin,
                    'vendor' => $request->user()->vendor ? [
                        'nama_mitra' => $request->user()->vendor->nama_mitra,
                        'status' => $request->user()->vendor->status, // PENTING: Kirim status ini
                        'is_verified' => $request->user()->vendor->is_verified,
                    ] : null,
                ] : null,
            ],
            // Jangan lupa flash message agar notifikasi sukses muncul
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
        ]);
    }
}
