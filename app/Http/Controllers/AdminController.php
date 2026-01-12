<?php

namespace App\Http\Controllers;

use App\Models\Report;
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
            'reports' => $reports
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // LOGIKA BARU SESUAI ALUR KAMU:
        
        // 1. Jika Admin melakukan VERIFIKASI (Input Harga & Kategori)
        if ($request->action === 'verify') {
            $request->validate([
                'price' => 'required|numeric|min:0',
                'category' => 'required|string',
            ]);

            $report->update([
                'price' => $request->price,
                'category' => $request->category,
                'status' => 'pending' // Masuk ke Menunggu Pembayaran
            ]);
        }

        // 2. Jika Admin KONFIRMASI PEMBAYARAN
        elseif ($request->action === 'confirm_payment') {
            $report->update([
                'status' => 'process' // Masuk ke Pengerjaan
            ]);
        }

        // 3. Jika Admin MENYELESAIKAN PESANAN
        elseif ($request->action === 'complete') {
            $report->update([
                'status' => 'completed',
                'progress' => 100
            ]);
        }

        // 4. Jika Admin MEMBATALKAN
        elseif ($request->action === 'cancel') {
            $report->update(['status' => 'cancelled']);
        }

        return back()->with('message', 'Laporan berhasil diperbarui!');
    }
}