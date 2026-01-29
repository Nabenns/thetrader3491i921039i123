<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $educationVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'education')
            ->orderBy('order')
            ->with(['videoProgress' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }, 'watchlist' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        $zoomRecapVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'zoom_recap')
            ->orderBy('order')
            ->with(['videoProgress' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }, 'watchlist' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();
            
        $featuredVideos = \App\Models\Video::where('is_published', true)
            ->where('is_featured', true)
            ->latest()
            ->get();

        return view('academy.index', compact('educationVideos', 'zoomRecapVideos', 'featuredVideos'));
    }

    public function marketWebinar()
    {
        $marketWebinarVideos = \App\Models\Video::where('is_published', true)
            ->where('category', 'market_webinar')
            ->orderBy('order')
            ->get();

        $upcomingWebinar = \App\Models\Webinar::where('is_active', true)
            ->where('schedule', '>', now())
            ->orderBy('schedule', 'asc')
            ->first();

        return view('market-webinar.index', compact('marketWebinarVideos', 'upcomingWebinar'));
    }

    public function submitTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
        ]);

        \App\Models\WebinarTopic::create([
            'user_id' => auth()->id(),
            'topic' => $request->topic,
        ]);

        return back()->with('success', 'Topik webinar berhasil dikirim! Terima kasih atas masukan Anda.');
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
            ->where('category', $video->category)
            ->where('id', '!=', $video->id)
            ->orderBy('order')
            ->get();

        $user = auth()->user();
        $progress = $user->videoProgress()->where('video_id', $video->id)->first();
        $note = $user->videoNotes()->where('video_id', $video->id)->first();

        return view('academy.show', compact('video', 'otherVideos', 'progress', 'note'));
    }

    public function toggleWatchlist(Request $request, \App\Models\Video $video)
    {
        $user = auth()->user();
        
        if ($user->watchlist()->where('video_id', $video->id)->exists()) {
            $user->watchlist()->detach($video->id);
            $status = 'removed';
        } else {
            $user->watchlist()->attach($video->id);
            $status = 'added';
        }

        return response()->json([
            'status' => $status,
            'message' => $status === 'added' ? 'Added to Watchlist' : 'Removed from Watchlist'
        ]);
    }

    public function markAsComplete(Request $request, \App\Models\Video $video)
    {
        $user = auth()->user();
        $progress = $user->videoProgress()->where('video_id', $video->id)->first();

        if ($progress && $progress->pivot->is_completed) {
            $user->videoProgress()->updateExistingPivot($video->id, [
                'is_completed' => false,
                'completed_at' => null
            ]);
            $status = 'incomplete';
        } else {
            if ($progress) {
                $user->videoProgress()->updateExistingPivot($video->id, [
                    'is_completed' => true,
                    'completed_at' => now()
                ]);
            } else {
                $user->videoProgress()->attach($video->id, [
                    'is_completed' => true,
                    'completed_at' => now()
                ]);
            }
            $status = 'completed';
        }

        return response()->json([
            'status' => $status,
            'message' => $status === 'completed' ? 'Marked as Complete' : 'Marked as Incomplete'
        ]);
    }

    public function saveNote(Request $request, \App\Models\Video $video)
    {
        $request->validate([
            'content' => 'nullable|string'
        ]);

        $user = auth()->user();
        
        $note = $user->videoNotes()->updateOrCreate(
            ['video_id' => $video->id],
            ['content' => $request->content]
        );

        return response()->json([
            'status' => 'saved',
            'message' => 'Note saved successfully'
        ]);
    }
}
