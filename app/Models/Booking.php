<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tour_schedule_id',
        'total_price',
        'special_requests',
        'status',
        'type',
        'members_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}