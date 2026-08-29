<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour_image extends Model
{
    protected $fillable = [
        'tour_id',
        'image_url',
        'is_primary',
        'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
