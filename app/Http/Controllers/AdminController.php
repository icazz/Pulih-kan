<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
                'drive_link' => $report->image_before,
                'status' => $report->status,
                'category' => $report->category ?? '-', // Kirim kategori
                'progress' => $report->progress ?? 0,
                // Kirim raw price (angka) untuk edit, dan formatted untuk display
                'price_raw' => $report->price, 
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Belum Ada Estimasi'
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'reports' => \App\Models\Report::with('user')->latest()->get(),
            // UBAH INI: Ambil semua vendor (hapus where is_verified false)
            // Agar kita bisa filter sendiri di frontend (Menunggu/Verified/Rejected)
            'vendors' => \App\Models\Vendor::latest()->get(),
        ]);
    }

    public function verifyVendor(Request $request, $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);

        if ($request->status === 'approve') {
            $vendor->update([
                'status' => 'verified',
                'is_verified' => true 
            ]);
            return redirect()->back()->with('message', 'Mitra berhasil disetujui.');
        } else {
            // Jangan dihapus, tapi ubah status jadi rejected
            $vendor->update([
                'status' => 'rejected',
                'is_verified' => false
            ]);
            return redirect()->back()->with('message', 'Mitra ditolak.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // --- LOGIKA UPDATE STATUS (Sama seperti sebelumnya) ---
        if ($request->action === 'verify') {
            $request->validate([
                'price' => 'required|numeric|min:0',
                'category' => 'required|string',
            ]);
            $report->update([
                'price' => $request->price,
                'category' => $request->category,
                'status' => 'pending'
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

        // --- PERBAIKAN DI SINI ---
        // Gunakan parameter ke-3 (303) untuk memaksa browser menggunakan GET saat redirect.
        return to_route('admin.dashboard', [], 303)
                ->with('message', 'Laporan berhasil diperbarui!');
    }

    public function show($id)
    {
        $report = Report::findOrFail($id); 
        
        return inertia('Admin/ReportDetail', [
            'report' => $report,
            'auth' => auth()->user()
        ]);
    }
}