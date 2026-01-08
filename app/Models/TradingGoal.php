<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingGoal extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'target_amount',
        'achieved_amount',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'achieved_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
