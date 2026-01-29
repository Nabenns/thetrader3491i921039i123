<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'video_url',
        'thumbnail',
        'description',
        'duration',
        'is_featured',
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    public function videoProgress()
    {
        return $this->belongsToMany(User::class, 'video_progress')
            ->withPivot('is_completed', 'completed_at')
            ->withTimestamps();
    }

    public function watchlist()
    {
        return $this->belongsToMany(User::class, 'watchlists')->withTimestamps();
    }
}
