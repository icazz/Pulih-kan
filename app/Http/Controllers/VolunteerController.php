<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // Import yang benar

class VolunteerController extends Controller
{
    public function create()
    {
        $existingData = null;
        
        if (Auth::check()) {
            $existingData = Volunteer::where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        // Panggil cukup dengan 'Inertia::render', jangan pakai backslash lagi di depannya
        return Inertia::render('Relawan', [
            'existingData' => $existingData
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'province' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'role' => 'required|string',
            'experience' => 'required|string',
            'agree' => 'accepted',
        ]);

        Volunteer::create([
            'user_id' => Auth::id(), 
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'emergency_contact' => $request->emergency_contact,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'role' => $request->role,
            'experience' => $request->experience,
            'status' => 'pending',
        ]);

        return back()->with('message', 'Pendaftaran berhasil dikirim!');
    }
}