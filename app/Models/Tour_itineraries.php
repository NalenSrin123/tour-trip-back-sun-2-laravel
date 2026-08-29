<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour_itineraries extends Model
{
    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
        'meals_included',
        'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
