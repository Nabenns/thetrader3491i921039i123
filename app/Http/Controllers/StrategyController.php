<?php

namespace App\Http\Controllers;

use App\Models\TradingStrategy;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function index()
    {
        $strategies = auth()->user()->tradingStrategies()->get();
        return view('strategies.index', compact('strategies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        auth()->user()->tradingStrategies()->create($validated);

        return redirect()->route('strategies.index')->with('success', 'Strategy created.');
    }

    public function update(Request $request, TradingStrategy $strategy)
    {
        if ($strategy->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $strategy->update($validated);

        return redirect()->route('strategies.index')->with('success', 'Strategy updated.');
    }

    public function destroy(TradingStrategy $strategy)
    {
        if ($strategy->user_id !== auth()->id()) {
            abort(403);
        }

        $strategy->delete(); // Linked journals will have strategy_id set to null

        return redirect()->route('strategies.index')->with('success', 'Strategy deleted.');
    }
}
