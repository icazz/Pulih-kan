<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
// 1. Panggil Facade Cloudinary Lagi
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    public function create()
    {
        return Inertia::render('Reports/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120', // Max 5MB
        ]);

        try {
            // 2. SIMPAN DATA LAPORAN KE DB
            $report = Report::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->deskripsi ?? $request->description ?? '-',
                'location' => $request->location,
                'status' => 'verification', 
                'video_url' => null, 
                'latitude' => $request->latitude ? $request->latitude : null,
                'longitude' => $request->longitude ? $request->longitude : null,
            ]);

            // 3. UPLOAD KE CLOUDINARY
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    
                    // --- UPLOAD KE CLOUDINARY ---
                    // Tidak perlu folder 'storage' lokal lagi
                    $uploadedFile = Cloudinary::upload($photo->getRealPath(), [
                        'folder' => 'laporan_banjir_foto'
                    ]);
                    
                    // Ambil URL HTTPS Aman
                    $secureUrl = $uploadedFile->getSecurePath();

                    // Simpan ke Tabel Attachments
                    ReportAttachment::create([
                        'report_id' => $report->id,
                        'file_url' => $secureUrl, // URL Cloudinary (https://res.cloudinary...)
                        'file_type' => 'image',
                    ]);

                    // Update Thumbnail Laporan
                    if ($index === 0) {
                        $report->update(['image_before' => $secureUrl]);
                    }
                }
            }

        } catch (\Exception $e) {
            return back()->withErrors(['photos' => 'Gagal upload ke Cloudinary: ' . $e->getMessage()]);
        }

        return redirect()->route('reports.index')->with('message', 'Laporan berhasil dikirim!');
    }

    // ... Index dan Show biarkan sama, karena frontend otomatis membaca URL ...
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())->with('attachments')->latest()->get();
        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                'image_url' => $report->image_before ?? 'https://placehold.co/600x400?text=No+Image',
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price' => $report->price ?? 'Menunggu Estimasi'
            ];
        });
        return Inertia::render('Reports/Index', ['reports' => $reports]);
    }

    public function show($id)
    {
        $report = Report::with('attachments')->findOrFail($id);
        if ($report->user_id !== Auth::id()) abort(403);

        return Inertia::render('Reports/Show', [
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'description' => $report->description,
                'location' => $report->location,
                'image_url' => $report->image_before,
                'attachments' => $report->attachments->map(fn($a) => $a->file_url),
                'status' => $report->status,
                'created_at_formatted' => $report->created_at->format('d M Y, H:i'),
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Menunggu Estimasi',
                'progress' => $report->progress ?? 0,
            ]
        ]);
    }
}