<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'broker',
        'account_number',
        'balance',
        'currency',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function journals()
    {
        return $this->hasMany(TradingJournal::class, 'account_id');
    }
}
