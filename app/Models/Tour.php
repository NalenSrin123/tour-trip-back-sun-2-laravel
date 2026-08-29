<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
