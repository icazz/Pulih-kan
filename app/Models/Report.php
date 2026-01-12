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
        'image_before',
        'video_url',
        'price',
        'progress',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Foto-foto Tambahan
    public function attachments()
    {
        // Pastikan kamu sudah membuat model ReportAttachment
        return $this->hasMany(ReportAttachment::class);
    }
}