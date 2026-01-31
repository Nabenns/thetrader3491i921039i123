<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingJournal extends Model
{
    protected $fillable = [
        'user_id',
        'pair',
        'type',
        'entry_price',
        'exit_price',
        'lot_size',
        'pnl',
        'pips',
        'status',
        'open_date',
        'close_date',
        'notes',
        'screenshot',
        'emotion',
        'strategy',
        'account_id',
        'commission',
        'swap',
        'tags',
        'magic_number',
    ];

    protected $casts = [
        'entry_price' => 'decimal:5',
        'exit_price' => 'decimal:5',
        'lot_size' => 'decimal:2',
        'pnl' => 'decimal:2',
        'pips' => 'decimal:2',
        'commission' => 'decimal:2',
        'swap' => 'decimal:2',
        'open_date' => 'datetime',
        'close_date' => 'datetime',
        'tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(TradingAccount::class, 'account_id');
    }
}
