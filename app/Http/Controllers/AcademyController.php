<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function index()
    {
        $videos = \App\Models\Video::where('is_published', true)->orderBy('order')->get();
        $webinars = \App\Models\Webinar::where('schedule', '>=', now())
            ->orderBy('schedule')
            ->get();
            
        return view('academy.index', compact('videos', 'webinars'));
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
