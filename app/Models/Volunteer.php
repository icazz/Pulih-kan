<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'emergency_contact',
        'province',
        'city',
        'address',
        'role',
        'experience',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
