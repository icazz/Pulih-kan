<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // HAPUS LOGIKA INI JIKA ADA:
    // if (Auth::check() && Auth::user()->vendor && Auth::user()->vendor->is_verified) {
    //     return redirect()->route('vendor.dashboard');
    // }

    // Biarkan Admin tetap redirect jika mau, atau hapus juga agar admin bisa lihat landing page
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => [
            'user' => Auth::user(),
        ],
    ]);
})->name('welcome');

// --- VENDOR REGISTRATION ---
Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.register');
Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store');

// --- USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.register');
    Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store'); 
    // Dashboard khusus Mitra (Vendor)
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // Detail & Penawaran (User bisa lihat penawaran)
    Route::get('/reports/history/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{id}/offer', [ReportController::class, 'offer'])->name('reports.offer');

    // Actions
    Route::put('/reports/{id}/cancel', [ReportController::class, 'cancel'])->name('reports.cancel');
    Route::delete('/reports/history/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Dummy
    Route::get('/donasi', function () { return "Halaman Donasi"; })->name('donation.create');
    Route::get('/tentang-kami', function () { return "Halaman Tentang Kami"; })->name('about.us');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () { 
    // Mengarah ke AdminController@index (sesuai controller yang kita buat)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/reports/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.updateStatus');
    Route::patch('/vendors/{id}/verify', [AdminController::class, 'verifyVendor'])->name('admin.verifyVendor');
});

require __DIR__.'/auth.php';