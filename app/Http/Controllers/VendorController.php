<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Report;

class VendorController extends Controller
{
    // 1. Tampilkan Halaman Form
    public function create()
    {
        $vendor = auth()->user()->vendor;
        if ($vendor && $vendor->status !== 'rejected') {
            return redirect()->route('vendor.dashboard');
        }

        return Inertia::render('Vendor/Register', [
            'existingData' => $vendor
        ]);
    }

    // 2. Proses Simpan / Update Data
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_mitra'   => 'required|string|max:255',
            'no_telepon'   => 'required|string|max:20',
            'email'        => 'required|email|max:255',
            'jenis_jasa'   => 'required|array|min:1',
            'jasa_lainnya' => 'nullable|string',
            'provinsi'     => 'required|string',
            'kota'         => 'required|string',
            'alamat'       => 'required|string',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'agreement'    => 'accepted',
        ]);
        Vendor::updateOrCreate(
            ['user_id' => Auth::id()], // Kunci pencarian
            array_merge($validated, [
                'status' => 'pending',     // Reset status jadi pending lagi
                'is_verified' => false     // Reset verifikasi jadi false
            ])
        );

        return redirect()->route('welcome')->with('message', 'Pendaftaran berhasil dikirim! Mohon tunggu verifikasi admin.');
    }

    public function dashboard()
    {
        $vendor = auth()->user()->vendor;
        // ... (logika proteksi/verifikasi sama seperti sebelumnya)

        $reports = Report::with('user')
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['pending', 'process', 'completed', 'cancelled'])
            ->latest()
            ->get();

        $transformedReports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'user_name' => $report->user->name ?? 'User',
                'title' => $report->title,
                'location' => $report->location,
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price_estimasi' => $report->price,
                'price_final' => $report->final_price,
                'date' => $report->created_at->format('d M Y'),
                'drive_link' => $report->drive_link, 
            ];
        });

        return Inertia::render('Vendor/Dashboard', [
            'vendor' => $vendor,
            'reports' => $transformedReports,
            'auth' => ['user' => auth()->user()] // Wajib untuk Navbar
        ]);
    }

    public function showReport($id)
    {
        $vendor = auth()->user()->vendor;
        $report = Report::with('user')->where('vendor_id', $vendor->id)->findOrFail($id);

        return Inertia::render('Vendor/ReportDetail', [
            'report' => $report,
            'vendor' => $vendor,
            'auth' => ['user' => auth()->user()] // Wajib untuk Navbar
        ]);
    }

    public function updateFinalPrice(Request $request, $id)
    {
        // Validasi
        $request->validate(['final_price' => 'required|numeric|min:0']);
        
        // Cari Report
        $report = Report::findOrFail($id);
        
        // Update Harga & Status
        $report->update([
            'final_price' => $request->final_price,
            'status'      => 'pending' // Ubah status agar User bisa melihat tombol "Bayar Sekarang"
        ]);

        return back()->with('message', 'Harga akhir berhasil diperbarui. Menunggu pembayaran user.');
    }

    public function cancelSelection($id)
    {
        $vendor = auth()->user()->vendor;
        $report = Report::where('vendor_id', $vendor->id)->findOrFail($id);

        // Hapus vendor_id agar pesanan kembali 'bebas' untuk dipilih mitra lain
        $report->update([
            'vendor_id' => null,
            // Status tetap 'pending' agar customer bisa pilih mitra lagi dari halaman Offer
        ]);

        return redirect()->route('vendor.dashboard')->with('message', 'Pesanan berhasil dikembalikan ke sistem.');
    }

    public function list()
    {
        // Tambahkan 'reports' di dalam with() agar datanya terambil
        $vendors = Vendor::where('status', 'verified')
            ->with(['reviews.report', 'reviews.user', 'reports']) // <--- TAMBAHKAN 'reports'
            ->get()
            ->map(function ($vendor) {
                $avgRating = $vendor->reviews->avg('rating');
                $reviewCount = $vendor->reviews->count();
                
                // LOGIKA HITUNG PROJECT SELESAI
                // Ambil report milik vendor ini, filter yang statusnya 'completed', lalu hitung
                $completedProjects = $vendor->reports->where('status', 'completed')->count();

                return [
                    'id' => $vendor->id,
                    'nama_mitra' => $vendor->nama_mitra,
                    'jenis_jasa' => $vendor->jenis_jasa,
                    'alamat' => $vendor->kota . ', ' . $vendor->provinsi,
                    'no_telepon' => $vendor->no_telepon,
                    'rating' => $avgRating ? round($avgRating, 1) : 0,
                    'review_count' => $reviewCount,
                    'initial' => substr($vendor->nama_mitra, 0, 1),
                    
                    // KIRIM DATA PROJECT SELESAI KE VUE
                    'project_count' => $completedProjects, 

                    // Data Ulasan (biarkan seperti sebelumnya)
                    'reviews_list' => $vendor->reviews->map(function($review) {
                        return [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'reviewer_name' => $review->user->name,
                            'project_name' => $review->report ? $review->report->title : 'Proyek',
                            'date' => $review->created_at->format('d M Y'),
                        ];
                    }),
                ];
            });

        return Inertia::render('Vendor/List', [
            'vendors' => $vendors,
            'auth' => ['user' => Auth::user()]
        ]);
    }

}