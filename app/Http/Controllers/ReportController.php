<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Vendor;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
// use Illuminate\Support\Facades\Storage;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    // --- 1. HALAMAN CREATE (Formulir) ---
    public function create()
    {
        return Inertia::render('Reports/Create', [
            'auth' => [
                'user' => Auth::user(), // Kirim data user agar Navbar tidak error
            ]
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'evidence.*' => 'nullable|mimes:jpg,jpeg,png,mp4,mov|max:10240',
            'deskripsi' => 'required', 
            'house_size' => 'nullable',
            'damage_types' => 'nullable|array',
        ]);

        $evidencePaths = [];

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                // Simpan file ke folder: storage/app/public/evidence
                // $path akan berisi string seperti "evidence/namafileunik.jpg"
                $path = $file->store('evidence', 'public');
                $evidencePaths[] = $path;
            }
        }

        // 2. Simpan ke Database
        Report::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'location' => $data['location'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'status' => 'verification',
            'evidence_files' => json_encode($evidencePaths),
            'description'  => $data['deskripsi'],  
            'house_size'   => $data['house_size'],    
            'damage_types' => $data['damage_types'],  
        ]);

        return redirect()->route('reports.index');
    }

    // --- 3. HALAMAN INDEX (Daftar Laporan) ---
    public function index()
    {
        // Mengambil laporan milik user yang sedang login
        $reports = Report::where('user_id', Auth::id())->latest()->get();

        $reports->transform(function ($report) {
            // Cek apakah mitra sudah menginput harga final
            $isFinal = !is_null($report->final_price);
            
            return [
                'id' => $report->id,
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                'evidence_files' => $report->evidence_files,
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                
                // --- LOGIKA BIAYA ---
                'has_final_price' => $isFinal, // Flag untuk mengecek "Biaya Fix" atau "Estimasi"
                'price' => $isFinal 
                    ? 'Rp ' . number_format($report->final_price, 0, ',', '.') 
                    : ($report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Menunggu Estimasi')
            ];
        });

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
    }

    public function show($id)
    {
        // 1. PENTING: Gunakan with('vendor') agar data mitra yang dipilih ikut terkirim ke Vue
        $report = Report::with(['user', 'vendor', 'review'])->findOrFail($id);
        
        if ($report->user_id !== Auth::id()) abort(403);

        return Inertia::render('Reports/Show', [
            // 2. Jangan di-transform manual satu per satu agar relasi 'vendor' tidak hilang
            // Kirim objek $report utuh, Laravel otomatis menyertakan relasi vendor di dalamnya
            'report' => $report,
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
    }

    public function offer($id)
    {
        $report = Report::with('user')->findOrFail($id);
        
        if ($report->user_id !== \Illuminate\Support\Facades\Auth::id()) abort(403);

        $latitude = $report->latitude;
        $longitude = $report->longitude;
        
        // 1. Ambil Checklist Kerusakan dari User
        // Contoh: ["Pembersihan Lumpur dan Puing", "Atap Bocor"]
        $damageTypes = $report->damage_types ?? []; 

        // 2. Query Dasar: Hitung Jarak & Filter Verified
        $query = \App\Models\Vendor::select('*')
            ->selectRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->where(function($q) {
                $q->where('status', 'verified')
                  ->orWhere('is_verified', true);
            });

        // 3. LOGIC PENCARIAN CERDAS (MATCHING JASA)
        if (!empty($damageTypes)) {
            $query->where(function($q) use ($damageTypes) {
                
                // Loop setiap keluhan user
                foreach ($damageTypes as $type) {
                    // Cari vendor yang punya jasa SAMA PERSIS dengan keluhan
                    // Contoh: User ceklis "Pembersihan Lumpur...", cari vendor yang punya layanan itu.
                    $q->orWhereJsonContains('jenis_jasa', $type);
                }
                
                // SELALU SERTAKAN: Vendor spesialis "Lainnya" (Palugada/Serabutan)
                $q->orWhereJsonContains('jenis_jasa', 'Lainnya');
            });
        } 
        else {
            // FALLBACK: Jika user tidak menceklis apapun, cari berdasarkan Kategori Umum
            if ($report->category === 'Ringan') {
                $query->where(function($q) {
                    $q->whereJsonContains('jenis_jasa', 'Pengecatan')
                      ->orWhereJsonContains('jenis_jasa', 'Perbaikan Minor')
                      ->orWhereJsonContains('jenis_jasa', 'Lainnya');
                });
            } elseif ($report->category === 'Sedang') {
                $query->where(function($q) {
                    $q->whereJsonContains('jenis_jasa', 'Kelistrikan')
                      ->orWhereJsonContains('jenis_jasa', 'Atap Bocor / Rusak')
                      ->orWhereJsonContains('jenis_jasa', 'Lainnya');
                });
            } else { // Berat
                 $query->where(function($q) {
                    $q->whereJsonContains('jenis_jasa', 'Renovasi Total')
                      ->orWhereJsonContains('jenis_jasa', 'Struktur Bangunan')
                      ->orWhereJsonContains('jenis_jasa', 'Lainnya');
                });
            }
        }

        // 4. Ambil 5 Mitra Terdekat yang Lolos Filter di atas
        $recommendedVendors = $query->orderBy('distance', 'asc')
            ->limit(5)
            ->get();

        $formattedPrice = 'Rp ' . number_format($report->price ?? 0, 0, ',', '.');

        return \Inertia\Inertia::render('Reports/Offer', [
            'report' => $report,
            'formattedPrice' => $formattedPrice,
            'recommendedVendors' => $recommendedVendors,
            'auth' => [
                'user' => \Illuminate\Support\Facades\Auth::user(),
            ]
        ]);
    }

    public function selectVendor(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        if ($report->user_id !== Auth::id()) abort(403);

        $request->validate([
            'vendor_id' => 'required|exists:vendors,id'
        ]);

        // Simpan Vendor Pilihan User & Lanjut ke Proses Berikutnya
        $report->update([
            'vendor_id' => $request->vendor_id,
            // Status bisa tetap 'pending' atau ubah ke 'waiting_payment' tergantung alur Anda
        ]);

        return redirect()->route('reports.show', $id)->with('success', 'Mitra berhasil dipilih! Silakan lakukan pembayaran.');
    }

    public function payment($id)
    {
        $report = Report::with(['vendor', 'user'])->findOrFail($id);
        
        // Proteksi: Hanya pemilik laporan yang bisa akses
        if ($report->user_id !== auth()->id()) abort(403);
        
        // Biaya admin (contoh statis seperti di lampiran)
        $admin_fee = 5000;
        $total_payment = $report->final_price + $admin_fee;

        return Inertia::render('Reports/Payment', [
            'report' => $report,
            'admin_fee' => $admin_fee,
            'total_payment' => $total_payment,
            'auth' => ['user' => auth()->user()]
        ]);
    }

    public function storePayment(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // 1. Validasi File
        $request->validate([
            'payment_type' => 'required',
            'proof'        => 'required|mimes:jpg,jpeg,png,pdf|max:5120', 
        ]);

        try {
            // 2. Upload File Bukti
            $path = null;
            if ($request->hasFile('proof')) {
                $path = $request->file('proof')->store('payment_proofs', 'public');
            }

            // 3. Update Database
            $report->update([
                'payment_method' => $request->payment_type,
                'payment_proof'  => $path ? url('storage/' . $path) : null,
                
                // --- PERBAIKAN DI SINI ---
                // Jangan ubah jadi 'payment_verification' karena database menolak.
                // Tetap 'pending' saja sesuai alur Anda.
                // Nanti Admin yang cek: Jika 'payment_proof' terisi, berarti butuh verifikasi.
                'status' => 'pending', 
            ]);

            return redirect()->route('reports.show', $id)
                ->with('message', 'Bukti pembayaran berhasil dikirim! Mohon tunggu konfirmasi admin.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function uploadContract(Request $request, $id)
    {
        $request->validate([
            'contract_file' => 'required|mimes:pdf|max:5120', 
        ]);

        $report = Report::findOrFail($id);
        
        // Pastikan hanya pemilik laporan yang bisa upload
        if ($report->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if ($request->hasFile('contract_file')) {
            // Hapus file lama jika ada
            if ($report->contract_file) {
                $oldPath = str_replace(url('storage/'), '', $report->contract_file);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan file baru (overwrite/ganti)
            $path = $request->file('contract_file')->store('contracts', 'public');
            
            $report->update([
                'contract_file' => url('storage/' . $path) 
            ]);
        }

        return back()->with('message', 'Kontrak berhasil diupload!');
    }

    public function complete($id)
    {
        // Cari laporan milik user yang sedang login
        $report = Report::where('user_id', Auth::id())->findOrFail($id);

        // Update status jadi completed dan progress 100%
        $report->update([
            'status' => 'completed',
            'progress' => 100
        ]);

        // --- PERBAIKAN UTAMA DI SINI ---
        // Tambahkan ->setStatusCode(303)
        // Ini memaksa browser menggunakan 'GET' saat redirect ke halaman show
        return to_route('reports.show', $id)
            ->with('message', 'Terima kasih! Pesanan telah ditandai selesai.')
            ->setStatusCode(303); 
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $report = Report::findOrFail($id);

        \App\Models\Review::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'vendor_id' => $report->vendor_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('message', 'Ulasan berhasil dikirim!');
    }

    // --- 6. ACTION LAINNYA ---
    public function cancel($id)
    {
        $report = Report::where('user_id', Auth::id())->findOrFail($id);
        $report->update(['status' => 'cancelled']);
        return back();
    }

    public function destroy($id)
    {
        $report = Report::where('user_id', Auth::id())->findOrFail($id);
        $report->delete();
        return redirect()->route('reports.index');
    }
}