<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')->latest()->get();

        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'user_name' => $report->user->name ?? 'User Tidak Dikenal',
                'title' => $report->title,
                'description' => $report->description,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                'drive_link' => $report->image_before, // Pastikan ini sesuai kolom database (misal: drive_link)
                'status' => $report->status,
                'category' => $report->category ?? '-',
                'progress' => $report->progress ?? 0,
                'price_raw' => $report->price, 
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Belum Ada Estimasi'
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'reports' => $reports, // Gunakan variabel yang sudah di-transform di atas
            'vendors' => Vendor::latest()->get(),
        ]);
    }

    public function verifyVendor(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($request->status === 'approve') {
            $vendor->update([
                'status' => 'verified',
                'is_verified' => true 
            ]);
            return redirect()->back()->with('message', 'Mitra berhasil disetujui.');
        } else {
            $vendor->update([
                'status' => 'rejected',
                'is_verified' => false
            ]);
            return redirect()->back()->with('message', 'Mitra ditolak.');
        }
    }

    // --- BAGIAN INI YANG DIPERBARUI ---
    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        if ($request->action === 'verify') {
            // 1. Tambahkan validasi vendor_id
            $request->validate([
                'price' => 'required|numeric|min:0',
                'category' => 'required|string',
            ]);

            // 2. Simpan vendor_id ke database
            $report->update([
                'price' => $request->price,
                'category' => $request->category,
                'status' => 'pending',
            ]);
        }
        elseif ($request->action === 'confirm_payment') {
            $report->update(['status' => 'process']);
        }
        elseif ($request->action === 'complete') {
            $report->update(['status' => 'completed', 'progress' => 100]);
        }
        elseif ($request->action === 'cancel') {
            $report->update(['status' => 'cancelled']);
        }

        // Redirect kembali (bisa ke dashboard atau tetap di halaman detail)
        return back()->with('message', 'Status laporan berhasil diperbarui!');
    }

    public function show($id)
    {
        $report = Report::with(['user', 'vendor'])->findOrFail($id);
        
        $latitude = $report->latitude;
        $longitude = $report->longitude;

        // --- DEBUGGING START (Tambahkan ini) ---
        // 1. Cek apakah koordinat laporan terbaca
        if (!$latitude || !$longitude) {
            dd("Koordinat Laporan Kosong/Null. Lat: $latitude, Long: $longitude");
        }
        // --- DEBUGGING END ---

        $nearestVendors = [];

        if ($latitude && $longitude) {
            $nearestVendors = Vendor::select('*')
                ->selectRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(latitude))
                    )) AS distance
                ", [$latitude, $longitude, $latitude])
                ->where('status', 'verified')     // <--- Pastikan ini sesuai database
                ->orWhere('is_verified', true)    // <--- Atau ini
                ->orderBy('distance', 'asc')
                ->limit(5)
                ->get();
            
            // --- DEBUGGING START ---
            // 2. Cek apakah query menemukan vendor?
            // dd($nearestVendors->toArray()); 
            // --- DEBUGGING END ---
        }

        return inertia('Admin/ReportDetail', [
            'report' => $report,
            'auth' => auth()->user(),
            'recommendedVendors' => $nearestVendors 
        ]);
    }
}