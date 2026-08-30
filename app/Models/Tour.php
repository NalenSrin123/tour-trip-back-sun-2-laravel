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
}
