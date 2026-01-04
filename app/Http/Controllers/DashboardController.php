<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Webinar;

class DashboardController extends Controller
{
    public function index()
    {
        $nextWebinar = Webinar::where('schedule', '>=', now())
            ->orderBy('schedule')
            ->first();

        return view('dashboard', compact('nextWebinar'));
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->latest()->paginate(10);
        return view('dashboard.transactions', compact('transactions'));
    }
}
