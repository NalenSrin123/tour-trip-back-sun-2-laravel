<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideAssignment extends Model
{
    protected $fillable = [
        'schedule_id',
        'guide_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(
            TourSchedule::class,
            'schedule_id',
            );
    }
    
    public function guide(): belongsTo
    {
        return $this->belongsTo(Guide::class);
    }

}
