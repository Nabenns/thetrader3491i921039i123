<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarTopic extends Model
{
    protected $fillable = ['user_id', 'topic', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
