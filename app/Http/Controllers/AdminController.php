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

    public function show($id)
    {
        $report = Report::with(['user', 'vendor'])->findOrFail($id);
        
        return inertia('Admin/ReportDetail', [
            'report' => $report,
            'auth' => auth()->user(),
        ]);
    }

    public function showVendor($id)
    {
        $vendor = Vendor::findOrFail($id);

        return Inertia::render('Admin/VendorDetail', [
            'vendor' => $vendor,
            'auth' => auth()->user()
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // 1. Verifikasi Laporan Awal (Input Harga & Kategori)
        if ($request->action === 'verify') {
            $request->validate([
                'price' => 'required|numeric|min:0',
                'category' => 'required|string',
            ]);

            $report->update([
                'price' => $request->price,
                'category' => $request->category,
                'status' => 'pending', 
            ]);
        }
        // 2. Konfirmasi Pembayaran
        elseif ($request->action === 'confirm_payment') {
            // Langsung ubah ke 'process' (database enum aman)
            $report->update(['status' => 'process']);
            
            return back()->with('message', 'Pembayaran dikonfirmasi. Status berubah menjadi Dalam Pengerjaan.');
        }
        // 3. Selesaikan Pekerjaan
        elseif ($request->action === 'complete') {
            $report->update(['status' => 'completed', 'progress' => 100]);
        }
        // 4. Batalkan Pesanan
        elseif ($request->action === 'cancel') {
            $report->update(['status' => 'cancelled']);
        }

        return back()->with('message', 'Status laporan berhasil diperbarui!');
    }
}