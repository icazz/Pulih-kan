<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Jangan lupa ini
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        // 1. Validasi: Gunakan nama variabel BAHASA INGGRIS (Sesuai Vue & Database)
        $data = $request->validate([
            'title' => 'required',
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'drive_link' => 'nullable|url',
            
            // PERUBAHAN DISINI: Samakan dengan Vue form
            'deskripsi' => 'required',        // Dulu: deskripsi
            'house_size' => 'nullable',         // Dulu: luas_rumah
            'damage_types' => 'nullable|array', // Dulu: kerusakan
        ]);

        // 2. Simpan ke Database
        Report::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'location' => $data['location'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'status' => 'verification',
            'drive_link' => $data['drive_link'],
            
            // PERUBAHAN DISINI: Tidak perlu mapping ribet lagi karena namanya sudah sama
            'description'  => $data['deskripsi'],  
            'house_size'   => $data['house_size'],    
            'damage_types' => $data['damage_types'],  
        ]);

        return redirect()->route('reports.index');
    }

    // --- 3. HALAMAN INDEX (Daftar Laporan) ---
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())->latest()->get();

        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                'drive_link' => $report->image_before, 
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Menunggu Estimasi'
            ];
        });

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            // PENTING: Kirim Auth User disini agar tidak blank setelah redirect
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
    }

    public function show($id)
    {
        // 1. PENTING: Gunakan with('vendor') agar data mitra yang dipilih ikut terkirim ke Vue
        $report = Report::with(['user', 'vendor'])->findOrFail($id);
        
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