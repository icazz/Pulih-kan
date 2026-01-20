<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    // Halaman Form Donasi (GET)
    public function index()
    {
        // Pastikan mengambil yang statusnya 'verified' (atau sesuai enum yang Anda gunakan)
        $donors = Donation::where('status', 'verified')->latest()->get();
        
        // Hitung total hanya dari yang sudah diverifikasi
        $totalDonation = Donation::where('status', 'verified')->sum('amount');

        return Inertia::render('Donasi', [
            'donors' => $donors,
            'totalDonation' => $totalDonation
        ]);
    }

    // Proses Simpan Donasi (POST)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:10000',
            'proof_file' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Upload File
        $path = null;
        if ($request->hasFile('proof_file')) {
            // Simpan di folder "public/proofs"
            $path = $request->file('proof_file')->store('proofs', 'public');
        }

        Donation::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'proof_file' => $path,
            'status' => 'pending',
        ]);

        return back()->with('message', 'Terima kasih! Donasi Anda sedang diverifikasi.');
    }
}