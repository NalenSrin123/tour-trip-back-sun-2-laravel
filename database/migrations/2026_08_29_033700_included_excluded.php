<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncludedExcluded extends Model
{
    protected $table = 'included_excluded';

    protected $fillable = [
        'tour_id',
        'type',
        'description',
        'status',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
