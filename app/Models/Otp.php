<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'is_used'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
