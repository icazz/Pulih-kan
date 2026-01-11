<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// --- VENDOR REGISTRATION ---
Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.register');
Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store');


// --- USER ROUTES (LOGIN REQUIRED) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD / LIST LAPORAN
    // Saya ubah namanya jadi 'reports.index' agar tombol "Kembali" di halaman detail berfungsi
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 2. FORM PENGAJUAN (Create & Store)
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // 3. DETAIL LAPORAN (Show) - SESUAI URL HISTORY ANDA
    // Ini yang menangani halaman detail saat panah diklik
    Route::get('/reports/history/{id}', [ReportController::class, 'show'])->name('reports.show');
    
    // 4. BATALKAN & HAPUS
    Route::put('/reports/{id}/cancel', [ReportController::class, 'cancel'])->name('reports.cancel');
    Route::delete('/reports/history/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');


    // --- DUMMY ROUTES ---
    Route::get('/donasi', function () { return "Halaman Donasi"; })->name('donation.create');
    Route::get('/tentang-kami', function () { return "Halaman Tentang Kami"; })->name('about.us');

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () { 
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports.index');
    Route::get('/reports/{id}', [AdminController::class, 'showReport'])->name('admin.reports.show');
    Route::patch('/reports/{id}/update', [AdminController::class, 'updateReportStatus'])->name('admin.reports.update');
});

require __DIR__.'/auth.php';