<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour_images extends Model
{
    protected $table = 'tour_images';

    protected $fillable = [
        'tour_id',
        'image_url',
        'is_primary',
        'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
