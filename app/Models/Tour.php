<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'title',
        'description',
        'destination',
        'base_price',
        'status',
    ];

    /**
     * Get all images for the tour.
     */
    public function images()
    {
        return $this->hasMany(Tour_images::class, 'tour_id');
    }

    /**
     * Get the primary/cover image for the tour.
     */
    public function primaryImage()
    {
        return $this->hasOne(Tour_images::class, 'tour_id')->where('is_primary', true);
    }
    public function inclusions()
    {
        return $this->hasMany(Included_excluded::class, 'tour_id');

    }
}
