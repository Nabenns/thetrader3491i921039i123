<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function index()
    {
        $educationVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'education')
            ->orderBy('order')
            ->get();

        $marketWebinarVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'market_webinar')
            ->orderBy('order')
            ->get();

        $zoomRecapVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'zoom_recap')
            ->orderBy('order')
            ->get();
            
        return view('academy.index', compact('educationVideos', 'marketWebinarVideos', 'zoomRecapVideos'));
    }

    public function show(\App\Models\Video $video)
    {
        if (! auth()->user()->hasActiveSubscription()) {
            return redirect()->route('subscription.index');
        }

        if (! $video->is_published) {
            abort(404);
        }

        $otherVideos = \App\Models\Video::where('is_published', true)
            ->where('id', '!=', $video->id)
            ->orderBy('order')
            ->get();

        return view('academy.show', compact('video', 'otherVideos'));
    }
}
