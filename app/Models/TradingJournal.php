<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'strategy_id',
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

    protected $appends = ['screenshot_url'];

    public function getScreenshotUrlAttribute()
    {
        return $this->screenshot ? Storage::url($this->screenshot) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(JournalImage::class);
    }

    public function account()
    {
        return $this->belongsTo(TradingAccount::class, 'account_id');
    }

    public function tradingStrategy()
    {
        return $this->belongsTo(TradingStrategy::class, 'strategy_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['pair'] ?? null, function ($query, $pair) {
            $query->where('pair', 'like', '%' . $pair . '%');
        })->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', $type);
        })->when($filters['outcome'] ?? null, function ($query, $outcome) {
            $query->where('status', $outcome);
        })->when($filters['account_id'] ?? null, function ($query, $accountId) {
            $query->where('account_id', $accountId);
        })->when($filters['strategy_id'] ?? null, function ($query, $strategyId) {
            $query->where('strategy_id', $strategyId);
        })->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
            $query->whereDate('open_date', '>=', $dateFrom);
        })->when($filters['date_to'] ?? null, function ($query, $dateTo) {
            $query->whereDate('open_date', '<=', $dateTo);
        });
    }
}
