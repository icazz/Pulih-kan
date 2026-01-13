<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'latitude',
        'longitude',
        'status',
        'category',
        'image_before', // Foto utama
        'video_url',    // Opsional (bisa dipakai atau tidak)
        'drive_link',   // TAMBAHAN: Link Google Drive
        'price',
        'progress',
        'damage_types', // TAMBAHAN: Array Checkbox
        'house_size',   // TAMBAHAN: Luas Rumah
    ];

    // PENTING: Casting agar 'damage_types' otomatis jadi Array saat diambil
    protected $casts = [
        'damage_types' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }
}