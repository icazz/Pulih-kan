<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // --- TAMBAHKAN INI ---
    protected $fillable = [
        'report_id',
        'user_id',
        'vendor_id',
        'rating',
        'comment',
    ];

    // Relasi ke User (Pembuat Review)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- TAMBAHKAN INI (YANG HILANG) ---
    // Agar Controller bisa mengambil judul laporan dari Review
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}