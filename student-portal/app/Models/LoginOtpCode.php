<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginOtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guard',
        'code_hash',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
