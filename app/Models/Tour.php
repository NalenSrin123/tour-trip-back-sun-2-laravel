<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'destination_id',
        'title',
        'slug',
        'duration_days',
        'duration_nights',
        'base_price',
        'price_override',
        'status',
    ];

    /**
     * Get all images for the tour.
     */
    public function tourImages()
    {
        return $this->hasMany(TourImages::class, 'tour_id');
    }

    /**
     * Get the primary/cover image for the tour.
     */
    public function primaryImage()
    {
        return $this->hasOne(TourImages::class, 'tour_id')->where('is_primary', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
    public function includeExclude()
    {
        return $this->hasMany(IncludedExcluded::class, 'tour_id');
    }
    public function tourItinerary()
    {
        return $this->hasMany(TourItinerary::class, 'tour_id');
    }
    public function tourSchedule()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id');
    }
}
