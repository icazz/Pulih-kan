<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    // Kolom apa saja yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'shop_name',
        'description',
        'service_type',
        'address',
        'latitude',
        'longitude',
        'is_verified',
        'ktp_photo_path',
    ];

    // Relasi Kebalikannya: Vendor ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}