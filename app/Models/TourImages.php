<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImages extends Model
{
    protected $table = 'tour_images';

    protected $fillable = [
        'tour_id',
        'image_url',
        'is_primary',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = [
        'full_image_url',
    ];

    /**
     * Get the full URL for the image.
     */
    public function getFullImageUrlAttribute(): ?string // by Som Chan Chav
    {
        if (!$this->image_url) {
            return null;
        }

        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        return asset('storage/' . $this->image_url);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
