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
    ];

    // Relasi: Setiap laporan pasti milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi Baru: Satu laporan punya BANYAK attachment
    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }
}