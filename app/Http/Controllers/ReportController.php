<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
// 1. IMPORT FACADE CLOUDINARY
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    public function create()
    {
        return Inertia::render('Reports/Create');
    }

    public function store(Request $request)
    {
        // 2. VALIDASI
        // Kita tambahkan validasi untuk 'video'
        $request->validate([
            'title' => 'required', // Saya sesuaikan field frontend Anda (title/deskripsi)
            'location' => 'required',
            'video' => 'nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:51200', // Max 50MB
            // Jika masih mau upload foto juga, biarkan di bawah ini:
            'foto' => 'nullable|image|max:10240', 
        ]);

        $videoUrl = null;
        $imageUrl = null;

        // 3. LOGIKA UPLOAD VIDEO KE CLOUDINARY
        if ($request->hasFile('video')) {
            // Upload Video
            $uploadedVideo = Cloudinary::uploadVideo($request->file('video')->getRealPath(), [
                'folder' => 'laporan_banjir_video',
                'resource_type' => 'video'
            ]);
            // Ambil Link Aman (https)
            $videoUrl = $uploadedVideo->getSecurePath();
        }

        // (Opsional) LOGIKA UPLOAD FOTO KE CLOUDINARY (Biar hemat storage Render juga)
        if ($request->hasFile('foto')) {
            $uploadedImage = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'laporan_banjir_foto'
            ]);
            $imageUrl = $uploadedImage->getSecurePath();
        }

        // 4. SIMPAN URL KE DATABASE
        // Pastikan tabel database Anda punya kolom 'video_url' atau 'image_before'
        Report::create([
            'user_id' => Auth::id(),
            'title' => $request->title ?? 'Laporan Baru',
            'description' => $request->deskripsi ?? '-',
            'location' => $request->location,
            'status' => 'pending',
            
            // Kita simpan LINK CLOUDINARY, bukan path lokal
            'video_url' => $videoUrl, 
            'image_before' => $imageUrl, // Simpan URL foto Cloudinary di sini
            
            // Field lain sesuaikan kebutuhan (latitude/longitude bisa null dulu kalau ribet)
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
        ]);

        return redirect()->route('reports.index')->with('message', 'Laporan berhasil dikirim ke Cloudinary!');
    }

    public function index()
    {
        $reports = Report::where('user_id', Auth::id())->latest()->get();

        // Format data untuk Frontend
        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                
                // PENTING: Karena sekarang isinya URL (https://...), kita tidak perlu tambah '/storage/' lagi
                'image_url' => $report->image_before ?? 'https://placehold.co/600x400?text=No+Image',
                'video_url' => $report->video_url, // Kirim URL video ke frontend
                
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price' => $report->price ?? 'Menunggu Estimasi'
            ];
        });

        return Inertia::render('Reports', ['reports' => $reports]);
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        // Format data tunggal
        $report->created_at_formatted = $report->created_at->format('d M Y');
        
        // Sama seperti index, langsung pakai URL dari database
        $report->image_url = $report->image_before ?? 'https://placehold.co/600x400?text=No+Image';
        
        // Logic dummy price/progress
        $report->price = 'Rp ' . number_format(rand(10000000, 50000000), 0, ',', '.');
        $report->progress = match($report->status) {
            'completed' => 100,
            'process' => 50,
            default => 0
        };

        return Inertia::render('Reports/Show', [
            'report' => $report
        ]);
    }
    
    // ... method cancel biarkan saja ...
}