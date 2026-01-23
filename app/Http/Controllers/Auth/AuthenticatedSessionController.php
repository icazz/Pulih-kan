<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException; // Wajib import ini untuk lempar error
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input (Cek apakah form diisi)
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            // Pesan Error jika input kosong
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email salah.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Coba Login (Cek apakah email & password cocok di database)
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // Jika gagal login, lempar error ke frontend
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        // 3. Regenerasi Sesi (Keamanan)
        $request->session()->regenerate();

        // 4. Logika Redirect (Admin vs User Biasa)
        // Pastikan di database tabel users ada kolom 'is_admin' atau 'role'
        // Jika menggunakan kolom 'role', ganti code di bawah jadi: $request->user()->role === 'admin'
        if ($request->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Redirect User Biasa ke halaman utama
        return redirect()->intended(route('welcome'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}