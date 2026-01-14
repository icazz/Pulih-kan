<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_mitra',    // Sebelumnya shop_name
        'no_telepon',    // Tambahan baru
        'email',         // Tambahan baru
        'jenis_jasa',    // Sebelumnya service_type
        'jasa_lainnya',  // Deskripsi untuk opsi "Lainnya"
        'provinsi',      // Tambahan baru
        'kota',          // Tambahan baru
        'alamat',        // Sebelumnya address
        'latitude',
        'longitude',
        'is_verified',
        'agreement',     // Status persetujuan user
        'status',
    ];

    /**
     * Casting data agar Laravel otomatis mengubah 
     * JSON di database menjadi Array di kodingan PHP.
     */
    protected $casts = [
        'jenis_jasa' => 'array',
        'is_verified' => 'boolean',
        'agreement' => 'boolean',
    ];

    /**
     * Relasi: Vendor milik seorang User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
{
    // Relasi: Satu vendor bisa mengerjakan banyak laporan
    return $this->hasMany(Report::class);
}
}