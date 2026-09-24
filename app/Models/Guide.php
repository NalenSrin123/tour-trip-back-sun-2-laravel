<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;


class Guide extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'license_number',
        'email',
        'phone_number',
        'languages',
        'specialties',
        'bio',
        'profile_image_url',
        'status',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function assignments(): HasMany
    {
        return $this->hasMany(GuideAssignment::class);
    }
}





























