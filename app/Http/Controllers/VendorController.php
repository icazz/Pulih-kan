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
        // Cek: Kalau sudah jadi vendor, jangan boleh masuk sini lagi
        if (Auth::user()->vendor) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Vendor/Register');
    }

    // 2. Proses Simpan Data
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'service_type' => 'required|string', // Contoh: Servis AC, Kelistrikan
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'ktp_photo' => 'required|image|max:2048', // Wajib upload KTP
        ]);

        // Upload KTP
        $path = null;
        if ($request->hasFile('ktp_photo')) {
            $path = $request->file('ktp_photo')->store('ktp_vendors', 'public');
        }

        // Simpan ke Database
        Vendor::create([
            'user_id' => Auth::id(),
            'shop_name' => $request->shop_name,
            'service_type' => $request->service_type,
            'description' => $request->description,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'ktp_photo_path' => $path,
            'is_verified' => false, // Default belum diverifikasi
        ]);

        return redirect()->route('dashboard')->with('message', 'Pendaftaran berhasil! Tunggu verifikasi admin.');
    }
}