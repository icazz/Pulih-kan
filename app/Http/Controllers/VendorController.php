<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Report;

class VendorController extends Controller
{
    // 1. Tampilkan Halaman Form
    public function create()
    {
        $vendor = auth()->user()->vendor;
        if ($vendor && $vendor->status !== 'rejected') {
            return redirect()->route('vendor.dashboard');
        }

        return Inertia::render('Vendor/Register', [
            'existingData' => $vendor
        ]);
    }

    // 2. Proses Simpan / Update Data
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_mitra'   => 'required|string|max:255',
            'no_telepon'   => 'required|string|max:20',
            'email'        => 'required|email|max:255',
            'jenis_jasa'   => 'required|array|min:1',
            'jasa_lainnya' => 'nullable|string',
            'provinsi'     => 'required|string',
            'kota'         => 'required|string',
            'alamat'       => 'required|string',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'agreement'    => 'accepted',
        ]);
        Vendor::updateOrCreate(
            ['user_id' => Auth::id()], // Kunci pencarian
            array_merge($validated, [
                'status' => 'pending',     // Reset status jadi pending lagi
                'is_verified' => false     // Reset verifikasi jadi false
            ])
        );

        return redirect()->route('welcome')->with('message', 'Pendaftaran berhasil dikirim! Mohon tunggu verifikasi admin.');
    }

    public function dashboard()
    {
        $vendor = auth()->user()->vendor;
        // ... (logika proteksi/verifikasi sama seperti sebelumnya)

        $reports = Report::with('user')
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['pending', 'process', 'completed', 'cancelled'])
            ->latest()
            ->get();

        $transformedReports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'user_name' => $report->user->name ?? 'User',
                'title' => $report->title,
                'location' => $report->location,
                'status' => $report->status,
                'progress' => $report->progress ?? 0,
                'price_estimasi' => $report->price,
                'price_final' => $report->final_price,
                'date' => $report->created_at->format('d M Y'),
                'drive_link' => $report->drive_link, 
            ];
        });

        return Inertia::render('Vendor/Dashboard', [
            'vendor' => $vendor,
            'reports' => $transformedReports,
            'auth' => ['user' => auth()->user()] // Wajib untuk Navbar
        ]);
    }

    public function showReport($id)
    {
        $vendor = auth()->user()->vendor;
        $report = Report::with('user')->where('vendor_id', $vendor->id)->findOrFail($id);

        return Inertia::render('Vendor/ReportDetail', [
            'report' => $report,
            'vendor' => $vendor,
            'auth' => ['user' => auth()->user()] // Wajib untuk Navbar
        ]);
    }

    public function updateFinalPrice(Request $request, $id)
    {
        $request->validate(['final_price' => 'required|numeric|min:0']);
        $report = Report::findOrFail($id);
        
        $report->update([
            'final_price' => $request->final_price,
            // Optional: Anda bisa merubah status ke 'process' otomatis jika sudah ada kontrak
        ]);

        return back()->with('message', 'Harga akhir berhasil diperbarui.');
    }

    public function cancelSelection($id)
    {
        $vendor = auth()->user()->vendor;
        $report = Report::where('vendor_id', $vendor->id)->findOrFail($id);

        // Hapus vendor_id agar pesanan kembali 'bebas' untuk dipilih mitra lain
        $report->update([
            'vendor_id' => null,
            // Status tetap 'pending' agar customer bisa pilih mitra lagi dari halaman Offer
        ]);

        return redirect()->route('vendor.dashboard')->with('message', 'Pesanan berhasil dikembalikan ke sistem.');
    }

}