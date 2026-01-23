<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'user_id', 'symbol', 'price', 'triggered'
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'triggered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
