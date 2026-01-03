<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    protected $guarded = [];

    protected $casts = [
        'schedule' => 'datetime',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];
}
