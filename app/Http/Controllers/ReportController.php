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

    // --- 2. PROSES SIMPAN (Store) ---
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'drive_link' => 'required|url',
        ]);

        try {
            Report::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->deskripsi ?? '-',
                'location' => $request->location,
                'status' => 'verification', 
                'image_before' => $request->drive_link,
                'video_url' => null,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // REDIRECT KE /reports (INDEX) SESUAI REQUEST KAMU
            return redirect()->route('reports.index')
                             ->with('message', 'Laporan berhasil dikirim! Menunggu verifikasi.');

        } catch (\Exception $e) {
            return back()->withErrors(['drive_link' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
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

    // --- 4. HALAMAN SHOW (Detail) ---
    public function show($id)
    {
        $report = Report::findOrFail($id);
        if ($report->user_id !== Auth::id()) abort(403);

        return Inertia::render('Reports/Show', [
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'description' => $report->description,
                'location' => $report->location,
                'drive_link' => $report->image_before,
                'status' => $report->status,
                'created_at_formatted' => $report->created_at->format('d M Y, H:i'),
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Menunggu Estimasi',
                'progress' => $report->progress ?? 0,
            ],
            // PENTING: Kirim Auth User disini juga
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
    }

    // --- 5. HALAMAN PENAWARAN (Offer) ---
    public function offer($id)
    {
        $report = Report::findOrFail($id);
        if ($report->user_id !== Auth::id()) abort(403);

        $formattedPrice = 'Rp ' . number_format($report->price ?? 0, 0, ',', '.');

        return Inertia::render('Reports/Offer', [
            'report' => $report,
            'formattedPrice' => $formattedPrice,
            // PENTING: Kirim Auth User disini juga
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
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