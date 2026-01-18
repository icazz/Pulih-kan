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
// Halaman Daftar Mitra
Route::get('/mitra-vendor', [VendorController::class, 'list'])->name('vendor.list');
// Halaman Detail Vendor
Route::get('/mitra-vendor/{id}', [VendorController::class, 'show'])->name('vendor.show');
// --- USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/vendor/register', [VendorController::class, 'create'])->name('vendor.register');
    Route::post('/vendor/register', [VendorController::class, 'store'])->name('vendor.store'); 
    // Dashboard khusus Mitra (Vendor)
    Route::prefix('vendor')->name('vendor.')->group(function () {
        // Dashboard Vendor
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
        
        // Detail Laporan sisi Vendor (Untuk Input Harga Akhir)
        // Nama rute ini harus: vendor.reports.show
        Route::get('/reports/{id}', [VendorController::class, 'showReport'])->name('reports.show');
        
        // Aksi Batalkan Pesanan oleh Mitra (Hapus vendor_id)
        Route::post('/reports/{id}/cancel-selection', [VendorController::class, 'cancelSelection'])->name('reports.cancelSelection');
        
        // Aksi Update Harga Akhir oleh Mitra
        Route::patch('/reports/{id}', [VendorController::class, 'updateFinalPrice'])->name('reports.updateFinalPrice');
        Route::patch('/reports/{id}/complete', [VendorController::class, 'completeProject'])->name('reports.complete');

    });

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // Detail & Penawaran (User bisa lihat penawaran)
    Route::get('/reports/history/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{id}/offer', [ReportController::class, 'offer'])->name('reports.offer');
    Route::post('/reports/{id}/select-vendor', [ReportController::class, 'selectVendor'])->name('reports.selectVendor');

    // Actions
    Route::put('/reports/{id}/cancel', [ReportController::class, 'cancel'])->name('reports.cancel');
    Route::delete('/reports/history/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment
    Route::get('/reports/{id}/payment', [ReportController::class, 'payment'])->name('reports.payment');
    Route::post('/reports/{id}/payment', [ReportController::class, 'storePayment'])->name('reports.storePayment');
    Route::patch('/reports/{id}/complete', [ReportController::class, 'complete'])->name('reports.complete');
    Route::post('/reports/{id}/review', [ReportController::class, 'storeReview'])->name('reports.review');

    // Dummy
    Route::get('/tentang-kami', function () { return "Halaman Tentang Kami"; })->name('about.us');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () { 
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/reports/{id}', [AdminController::class, 'show'])->name('admin.reports.show');
    Route::patch('/reports/{id}', [AdminController::class, 'update'])->name('admin.reports.update');
    Route::patch('/reports/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.updateStatus');
    Route::patch('/vendors/{id}/verify', [AdminController::class, 'verifyVendor'])->name('admin.verifyVendor');
});

require __DIR__.'/auth.php';