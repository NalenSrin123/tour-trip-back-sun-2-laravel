<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class included_excluded extends Model
{
    protected $table = 'tour_inclusions';

    protected $fillable = [
        'tour_id',
        'type',
        'description',
        'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
