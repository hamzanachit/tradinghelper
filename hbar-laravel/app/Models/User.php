<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'array',
        'password' => 'hashed',
    ];

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function drawings()
    {
        return $this->hasMany(Drawing::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function watchlist()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class)->where('status', 'active');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
