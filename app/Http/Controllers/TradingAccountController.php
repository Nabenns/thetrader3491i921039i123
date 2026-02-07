<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;

class TradingAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'broker' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $account = auth()->user()->tradingAccounts()->create([
            'name' => $validated['name'],
            'broker' => $validated['broker'],
            'balance' => $validated['balance'],
            'currency' => strtoupper($validated['currency']),
            'is_default' => !auth()->user()->tradingAccounts()->exists(), // Set as default if it's the first one
        ]);

        return redirect()->back()->with('success', 'Trading account created successfully: ' . $account->name);
    }
}
