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
            // 1. Data User (Auth)
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email, // Tambahkan email biar lengkap
                    'is_admin' => $request->user()->is_admin,
                    'vendor' => $request->user()->vendor ? [
                        'nama_mitra' => $request->user()->vendor->nama_mitra,
                        'status' => $request->user()->vendor->status,
                        'is_verified' => $request->user()->vendor->is_verified,
                    ] : null,
                ] : null,
            ],

            // 2. Data Flash Message (Sukses)
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'status' => fn () => $request->session()->get('status'), // Tambahkan ini untuk status register
            ],

            // 3. INI YANG HILANG: Data Error Validasi
            // Tanpa ini, form.errors di Vue tidak akan pernah muncul!
            'errors' => fn () => $request->session()->get('errors')
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : (object) [],
        ]);
    }
}