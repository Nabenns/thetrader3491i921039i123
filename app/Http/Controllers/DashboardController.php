<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Webinar;

class DashboardController extends Controller
{
    public function index()
    {
        $latestMarketWebinar = \App\Models\Video::where('category', 'market_webinar')
            ->where('is_published', true)
            ->latest()
            ->first();

        return view('dashboard', compact('latestMarketWebinar'));
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->latest()->paginate(10);
        return view('dashboard.transactions', compact('transactions'));
    }
}
