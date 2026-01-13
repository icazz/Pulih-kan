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

    public function offer($id)
    {
        // REVISI DISINI: Tambahkan with('vendor')
        $report = Report::with('vendor')->findOrFail($id);
        
        if ($report->user_id !== Auth::id()) abort(403);

        $formattedPrice = 'Rp ' . number_format($report->price ?? 0, 0, ',', '.');

        return Inertia::render('Reports/Offer', [
            'report' => $report,
            'formattedPrice' => $formattedPrice,
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