<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Included_excluded extends Model
{
    protected $fillable = [
        'tour_id',
        'type',
        'description',
        'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
