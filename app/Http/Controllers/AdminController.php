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
        
        return inertia('Admin/ReportDetail', [
            'report' => $report,
            'auth' => auth()->user(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);

        $report->update([
            'price' => $request->price,
            'category' => $request->category,
            'status' => 'pending',
        ]);

        return back()->with('message', 'Penawaran berhasil dikirim!');
    }
}