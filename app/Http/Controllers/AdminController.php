<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Report;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            // Kirim statistik ke frontend
            'stats' => [
                'total_users' => User::where('role', 'user')->count(),
                'total_vendors' => Vendor::where('is_verified', true)->count(),
                'pending_vendors' => Vendor::where('is_verified', false)->count(),
                'total_reports' => Report::count(),
                'pending_reports' => Report::where('status', 'pending')->count(),
            ],
            // Kirim 5 daftar vendor terbaru yang MENUNGGU verifikasi
            'pending_verifications' => Vendor::with('user')
                ->where('is_verified', false)
                ->latest()
                ->take(5)
                ->get()
        ]);
    }

    public function reports()
    {
        $reports = Report::with('user') // Ambil data pelapornya juga
            ->latest()
            ->get();

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports
        ]);
    }

    // 2. Tampilkan Detail Laporan (Versi Admin)
    public function showReport($id)
    {
        $report = Report::with(['user', 'attachments'])->findOrFail($id);

        return Inertia::render('Admin/Reports/Show', [
            'report' => $report
        ]);
    }

    // 3. Update Status Laporan (PENTING)
    public function updateReportStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,process,done'
        ]);

        // Cari laporan dan update
        $report = Report::findOrFail($id);
        $report->update(['status' => $request->status]);

        return back()->with('message', 'Status laporan berhasil diperbarui!');
    }
}