<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'name',
        'sex',
        'age_group',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
