<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAttachment; // Jangan lupa import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    public function create()
    {
        return Inertia::render('Reports/Create');
    }

    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            // Video: Max 50MB
            'video' => 'nullable|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:51200', 
            // Foto: Array, Max 5MB per foto
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120', 
        ]);

        $videoUrl = null;
        $thumbnailUrl = null; // Foto pertama akan jadi thumbnail

        try {
            // 2. UPLOAD VIDEO (Single)
            if ($request->hasFile('video')) {
                $uploadedVideo = Cloudinary::uploadVideo($request->file('video')->getRealPath(), [
                    'folder' => 'laporan_banjir_video',
                    'resource_type' => 'video'
                ]);
                $videoUrl = $uploadedVideo->getSecurePath();
            }

            // 3. SIMPAN LAPORAN UTAMA (Create Report)
            $report = Report::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                // Handle deskripsi null/kosong
                'description' => $request->deskripsi ?? $request->description ?? '-',
                'location' => $request->location,
                'status' => 'pending',
                'video_url' => $videoUrl,
                // Latitude/Longitude: Pastikan null jika kosong (Postgres Strict)
                'latitude' => $request->latitude ? $request->latitude : null,
                'longitude' => $request->longitude ? $request->longitude : null,
            ]);

            // 4. UPLOAD BANYAK FOTO (Multiple)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    // Upload ke Cloudinary
                    $uploadedPhoto = Cloudinary::upload($photo->getRealPath(), [
                        'folder' => 'laporan_banjir_foto'
                    ]);
                    $secureUrl = $uploadedPhoto->getSecurePath();

                    // Simpan ke Tabel Attachments
                    ReportAttachment::create([
                        'report_id' => $report->id,
                        'file_url' => $secureUrl,
                        'file_type' => 'image',
                    ]);

                    // Jadikan foto pertama sebagai thumbnail di tabel utama
                    if ($index === 0) {
                        $report->update(['image_before' => $secureUrl]);
                    }
                }
            }

        } catch (\Exception $e) {
            // Jika error, kembalikan ke form dengan pesan
            return back()->withErrors(['video' => 'Gagal upload: ' . $e->getMessage()]);
        }

        return redirect()->route('reports.index')->with('message', 'Laporan berhasil dikirim!');
    }

    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
                    ->with('attachments') // Load data foto tambahan
                    ->latest()
                    ->get();

        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                // Tampilkan foto pertama atau placeholder
                'image_url' => $report->image_before ?? 'https://placehold.co/600x400?text=No+Image',
                'video_url' => $report->video_url,
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price' => $report->price ?? 'Menunggu Estimasi'
            ];
        });

        return Inertia::render('Reports/Index', ['reports' => $reports]);
    }

    public function show($id)
    {
        // Load attachment saat detail dibuka
        $report = Report::with('attachments')->findOrFail($id);

        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Reports/Show', [
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'description' => $report->description,
                'location' => $report->location,
                // List semua foto untuk slider/gallery di frontend
                'attachments' => $report->attachments->map(fn($a) => $a->file_url), 
                'image_url' => $report->image_before, 
                'video_url' => $report->video_url,
                'status' => $report->status,
                'created_at_formatted' => $report->created_at->format('d M Y, H:i'),
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Menunggu Estimasi',
                'progress' => $report->progress ?? 0,
            ]
        ]);
    }
}