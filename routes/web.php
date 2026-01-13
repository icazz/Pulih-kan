<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'auth' => [
            'user' => Auth::user(), // Perbaikan agar tidak error saat load Navbar
        ],
    ]);
})->name('welcome');

// --- VENDOR REGISTRATION ---
Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.register');
Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store');

// --- USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {
    
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
});

// --- ROUTE DARURAT (Hanya untuk update database) ---
Route::get('/update-database-darurat', function () {
    try {
        // Jalanin migrasi database (tambah kolom baru)
        \Illuminate\Support\Facades\Artisan::call('migrate --force');
        
        // Bersihin cache biar settingan baru kebaca
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        return "<h1>BERHASIL! ✅</h1><p>Database sudah di-update. Kolom video_url sudah ditambahkan.</p>";
    } catch (\Exception $e) {
        return "<h1>GAGAL ❌</h1><p>" . $e->getMessage() . "</p>";
    }
});

require __DIR__.'/auth.php';