<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VendorController extends Controller
{
    // 1. Tampilkan Halaman Form
    public function create()
    {
        $vendor = auth()->user()->vendor;

        // PERUBAHAN 1:
        // Cek jika vendor sudah ada DAN statusnya BUKAN 'rejected'.
        // Artinya: Kalau statusnya 'rejected', kode ini dilewati agar user bisa buka form lagi.
        if ($vendor && $vendor->status !== 'rejected') {
            return redirect()->route('vendor.dashboard');
        }

        // Kirim data lama (existingData) ke Vue agar form terisi otomatis saat daftar ulang
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

        // PERUBAHAN 2:
        // Hapus pengecekan "if (Auth::user()->vendor)" yang lama agar tidak error saat daftar ulang.
        
        // Gunakan updateOrCreate.
        // Logic: Cari data berdasarkan user_id.
        // Jika ketemu (misal data lama yang ditolak), UPDATE datanya.
        // Jika tidak ketemu, CREATE baru.
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

        // 1. Jika belum mendaftar, lempar ke form pendaftaran
        if (!$vendor) {
            return redirect()->route('vendor.register');
        }

        // PERUBAHAN 3:
        // Jika statusnya DITOLAK ('rejected'), jangan kasih masuk dashboard/waiting.
        // Lempar balik ke form pendaftaran untuk perbaiki data.
        if ($vendor->status === 'rejected') {
            return redirect()->route('vendor.register');
        }

        // 2. Jika status masih pending / belum diverifikasi
        // (Support status 'pending' string atau boolean false lama)
        if ($vendor->status === 'pending' || (!$vendor->status && !$vendor->is_verified)) {
            return Inertia::render('Vendor/WaitingVerification');
        }

        // 3. Jika SUDAH diverifikasi
        return Inertia::render('Vendor/Dashboard', ['vendor' => $vendor]);
    }
}