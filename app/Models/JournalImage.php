<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalImage extends Model
{
    use HasFactory;

    protected $fillable = ['trading_journal_id', 'image_path', 'type'];

    public function tradingJournal()
    {
        return $this->belongsTo(TradingJournal::class);
    }
}
