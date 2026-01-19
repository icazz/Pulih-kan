<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'provinsi'     => 'required|string|max:100',
            'kota'         => 'required|string|max:100',
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

        return redirect()->route('vendor.dashboard');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        // 1. Cek apakah user sudah daftar jadi vendor?
        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // 2. LOGIKA UTAMA: Cek Status
        // Jika status masih pending, tampilkan halaman Tunggu Verifikasi
        if ($vendor->status === 'pending') {
            return Inertia::render('Vendor/WaitingVerification', [
                'vendor' => $vendor,
                'auth' => ['user' => $user] // Tetap kirim auth untuk Navbar
            ]);
        }

        // 3. Jika status Rejected (Ditolak), lempar ke halaman perbaikan data
        if ($vendor->status === 'rejected') {
            return redirect()->route('vendor.register')
                ->with('error', 'Maaf, pendaftaran Anda ditolak. Silakan perbaiki data.');
        }

        // 4. Jika lolos (status == verified), baru jalankan query Dashboard yang berat ini
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
            'auth' => ['user' => $user]
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

        // --- TAMBAHKAN PENGECEKAN INI ---
        if ($report->final_price) {
            return back()->with('error', 'Anda tidak dapat membatalkan pesanan yang sudah diberi harga tetap. Hubungi admin jika terjadi kesalahan.');
        }
        // -------------------------------

        // Logika pembatalan yang lama
        $report->update([
            'vendor_id' => null,
            // 'status' => 'pending' // Opsional: reset status jika perlu
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

    public function show($id)
    {
        $vendor = Vendor::with(['reviews.user', 'reviews.report', 'reports'])
            ->withCount(['reports as completed_projects' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->findOrFail($id);

        // Hitung Rata-rata Rating
        $avgRating = $vendor->reviews->avg('rating') ?? 0;
        
        // Hitung Distribusi Bintang (5, 4, 3, 2, 1)
        $ratingDist = [
            5 => $vendor->reviews->where('rating', 5)->count(),
            4 => $vendor->reviews->where('rating', 4)->count(),
            3 => $vendor->reviews->where('rating', 3)->count(),
            2 => $vendor->reviews->where('rating', 2)->count(),
            1 => $vendor->reviews->where('rating', 1)->count(),
        ];

        // Format Ulasan
        $reviews = $vendor->reviews()->latest()->get()->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'project_name' => $review->report ? $review->report->title : 'Proyek Umum',
                'rating' => $review->rating,
                'comment' => $review->comment,
                'date' => $review->created_at->format('d F Y'),
                'user_initial' => substr($review->user->name, 0, 1),
            ];
        });

        return Inertia::render('Vendor/Show', [
            'vendor' => [
                'id' => $vendor->id,
                'nama_mitra' => $vendor->nama_mitra,
                'alamat' => $vendor->kota . ', ' . $vendor->provinsi, // Sesuaikan kolom alamat Anda
                'no_telepon' => $vendor->no_telepon,
                'email' => 'vendor@email.com', // Dummy jika tidak ada kolom email
                'project_count' => $vendor->completed_projects,
                'rating' => round($avgRating, 1),
                'review_count' => $vendor->reviews->count(),
                'initial' => substr($vendor->nama_mitra, 0, 1),
            ],
            'ratingDist' => $ratingDist,
            'reviews' => $reviews,
            'auth' => ['user' => Auth::user()]
        ]);
    }

    public function uploadContract(Request $request, $id)
{
    // 1. Cek apakah request membawa file?
    if (!$request->hasFile('contract_file')) {
        dd("File tidak terdeteksi oleh Server. Cek 'post_max_size' di php.ini atau payload Frontend.");
    }

    // 2. Cek apakah file valid?
    if (!$request->file('contract_file')->isValid()) {
        dd("File terdeteksi tapi corrupt atau error saat upload.");
    }

    $request->validate([
        'contract_file' => 'required|mimes:pdf|max:5120', 
    ]);

    $report = Report::findOrFail($id);
    
    // Hapus file lama logic...
    if ($report->contract_file) {
        $oldPath = str_replace(url('storage/'), '', $report->contract_file);
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    // Simpan
    $path = $request->file('contract_file')->store('contracts', 'public');
    
    // DEBUG: Cek path yang dihasilkan
    // dd($path); <--- Uncomment ini jika ingin melihat path file

    // Update
    $report->update([
        'contract_file' => url('storage/' . $path) 
    ]);

    return back()->with('message', 'Kontrak berhasil diupload!');
}

}