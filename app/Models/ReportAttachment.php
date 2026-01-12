<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'file_url',
        'file_type', // 'image' atau 'video' (tapi video kita taruh di tabel utama aja biar simpel)
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}