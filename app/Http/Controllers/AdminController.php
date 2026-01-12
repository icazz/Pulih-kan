<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil semua laporan, urutkan terbaru, load data usernya
        $reports = Report::with('user')->latest()->get();

        // Format data untuk frontend
        $reports->transform(function ($report) {
            return [
                'id' => $report->id,
                'user_name' => $report->user->name ?? 'User Tidak Dikenal', // Nama User
                'title' => $report->title,
                'location' => $report->location,
                'date' => $report->created_at->format('d M Y'),
                'image_url' => $report->image_before ?? 'https://placehold.co/600x400?text=No+Image',
                'drive_link' => $report->image_before, // Asumsi link drive disimpan disini
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price' => $report->price ? 'Rp ' . number_format($report->price, 0, ',', '.') : 'Belum Ada Estimasi'
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'reports' => $reports
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:verification,pending,process,completed,cancelled'
        ]);

        $report->update(['status' => $request->status]);

        return back()->with('message', 'Status laporan berhasil diperbarui!');
    }
}