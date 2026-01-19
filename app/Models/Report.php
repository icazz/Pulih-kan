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
        'evidence_files',
        'price',
        'progress',
        'damage_types', 
        'house_size',   
        'vendor_id',
        'final_price',  
        'contract_file',
        'payment_method', 
        'payment_proof',
    ];

    protected $casts = [
        'damage_types' => 'array',
        'evidence_files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}