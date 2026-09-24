<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourSchedule extends Model
{
    protected $fillable = [
        'tour_id',
        'start_datetime',
        'end_datetime',
        'booking_cutoff_datetime',
        'min_capacity',
        'max_capacity',
        'current_booked',
        'version',
        'price_override',
        'status',
        'notes',
    ];
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'booking_cutoff_datetime' => 'datetime',
        'price_override' => 'decimal:2',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tour_schedule_id');
    }

    public function guideAssignments(): HasMany
    {
        return $this->hasMany(GuideAssignment::class, 'schedule_id');
    }

    
}
