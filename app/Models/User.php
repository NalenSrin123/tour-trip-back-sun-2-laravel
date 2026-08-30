<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
<<<<<<< HEAD
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourSchedule()
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function bookingPartials()
    {
        return $this->hasMany(BookingPartial::class);
=======

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
>>>>>>> 9222cc9be9d429e4aac606bb0af919e539b45b56
    }
}
