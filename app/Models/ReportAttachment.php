<?php

namespace App\Models;

// 👇 JANGAN LUPA BARIS INI (Import Library-nya)
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class ReportAttachment extends Model
{
    use HasFactory; // Fitur ini butuh baris 'use' di atas tadi

    protected $fillable = ['report_id', 'file_path', 'file_type'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}