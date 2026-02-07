<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingStrategy extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'color'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradingJournals()
    {
        return $this->hasMany(TradingJournal::class, 'strategy_id');
    }
}
