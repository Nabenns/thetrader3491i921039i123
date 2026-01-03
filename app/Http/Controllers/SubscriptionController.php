<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = \App\Models\Package::where('is_active', true)->get();
        return view('subscription.index', compact('packages'));
    }
}
