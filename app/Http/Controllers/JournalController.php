<?php

namespace App\Http\Controllers;

use App\Models\TradingJournal;
use App\Models\TradingGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class JournalController extends Controller
{
    public function downloadTemplate(\App\Services\TradeImportService $importService)
    {
        return $importService->generateTemplate();
    }

    public function import(Request $request, \App\Services\TradeImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'account_id' => 'required|exists:trading_accounts,id',
        ]);

        $result = $importService->import(
            $request->file('file'),
            auth()->id(),
            $request->account_id
        );

        if (count($result['errors']) > 0) {
            return redirect()->back()->with('error', 'Import completed with errors: ' . implode(', ', $result['errors']));
        }

        return redirect()->back()->with('success', "Successfully imported {$result['count']} trades.");
    }

    public function export(Request $request)
    {
        $query = auth()->user()->tradingJournals()->latest('open_date');

        // Apply Filters (Same as index)
        if ($request->filled('date_from'))
            $query->whereDate('open_date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('open_date', '<=', $request->date_to);
        if ($request->filled('pair'))
            $query->where('pair', $request->pair);
        if ($request->filled('type'))
            $query->where('type', $request->type);
        if ($request->filled('outcome')) {
            if ($request->outcome === 'win')
                $query->where('pnl', '>', 0);
            elseif ($request->outcome === 'loss')
                $query->where('pnl', '<', 0);
            elseif ($request->outcome === 'break_even')
                $query->where('pnl', '=', 0);
        }
        if ($request->filled('account_id'))
            $query->where('account_id', $request->account_id);

        $trades = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=journal_export_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($trades) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Pair', 'Type', 'Entry Price', 'Exit Price', 'Lot Size', 'PnL', 'Pips', 'Status', 'Strategy', 'Emotion', 'Notes']);

            foreach ($trades as $trade) {
                fputcsv($file, [
                    $trade->open_date->format('Y-m-d H:i'),
                    $trade->pair,
                    $trade->type,
                    $trade->entry_price,
                    $trade->exit_price,
                    $trade->lot_size,
                    $trade->pnl,
                    $trade->pips,
                    $trade->status,
                    $trade->strategy,
                    $trade->emotion,
                    $trade->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function index(Request $request, \App\Services\CurrencyService $currencyService)
    {
        $query = auth()->user()->tradingJournals()->latest('open_date');

        // Apply Filters
        if ($request->filled('date_from')) {
            $query->whereDate('open_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('open_date', '<=', $request->date_to);
        }
        if ($request->filled('pair')) {
            $query->where('pair', $request->pair);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('outcome')) {
            if ($request->outcome === 'win') {
                $query->where('pnl', '>', 0);
            } elseif ($request->outcome === 'loss') {
                $query->where('pnl', '<', 0);
            } elseif ($request->outcome === 'break_even') {
                $query->where('pnl', '=', 0);
            }
        }

        // Account Filter
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Clone query for stats (so pagination doesn't affect totals)
        $statsJournals = (clone $query)->get();

        $journals = $query->paginate(10)->withQueryString();
        $goal = auth()->user()->tradingGoals()->where('month', now()->month)->where('year', now()->year)->first();

        // Calculate stats (based on filtered journals)
        $totalTrades = $statsJournals->count();
        $winRate = $totalTrades > 0 ? ($statsJournals->where('pnl', '>', 0)->count() / $totalTrades) * 100 : 0;
        $profitFactor = $statsJournals->where('pnl', '<', 0)->sum('pnl') != 0
            ? abs($statsJournals->where('pnl', '>', 0)->sum('pnl') / $statsJournals->where('pnl', '<', 0)->sum('pnl'))
            : 0;
        $totalPnL = $statsJournals->sum('pnl');

        // Equity Curve Data (Cumulative PnL over time) - ALWAYS GLOBAL (Unfiltered)
        $closedTrades = auth()->user()->tradingJournals()
            ->where('status', '!=', 'open')
            ->get()
            ->sortBy(function ($trade) {
                return $trade->close_date ?? $trade->open_date;
            });

        $dailyPnL = $closedTrades->groupBy(function ($trade) {
            return ($trade->close_date ?? $trade->open_date)->format('Y-m-d');
        })->map(function ($dayTrades) {
            return $dayTrades->sum('pnl');
        });

        // Initialize equity arrays with start point
        $equityDates = [];
        $equityValues = [];
        $cumulative = 0;

        foreach ($dailyPnL as $date => $pnl) {
            $cumulative += $pnl;
            $equityDates[] = $date;
            $equityValues[] = $cumulative;
        }

        // Heatmap Data (Last 365 Days)
        $heatmapData = $closedTrades->where('open_date', '>=', now()->subYear())->groupBy(function ($trade) {
            return ($trade->close_date ?? $trade->open_date)->format('Y-m-d');
        })->map(function ($dayTrades) {
            return [
                'pnl' => $dayTrades->sum('pnl'),
                'count' => $dayTrades->count(),
            ];
        });

        $accounts = auth()->user()->tradingAccounts;

        // Calculate Equity Meta for Chart Tooltips
        $equityMeta = [];
        $runningPnL = 0;

        // We need to re-iterate or align with equityDates/values logic
        // Since we built equityValues from dailyPnL, let's build meta from it too
        if ($dailyPnL->isNotEmpty()) {
            // Add initial point metadata
            $equityMeta[] = ['pnl' => 0, 'count' => 0];
        }

        foreach ($dailyPnL as $date => $pnl) {
            $runningPnL += $pnl;
            // Get trade count for this day
            $dayCount = $closedTrades->filter(function ($t) use ($date) {
                return ($t->close_date ?? $t->open_date)->format('Y-m-d') === $date;
            })->count();

            $equityMeta[] = ['pnl' => $pnl, 'count' => $dayCount];
        }



        // --- Advanced Analytics (Day & Session) ---
        // Days of Week
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        $winRateByDay = [];
        foreach ($days as $day) {
            $dayTrades = $statsJournals->filter(fn($t) => $t->open_date->format('D') === $day);
            $count = $dayTrades->count();
            $winRateByDay[$day] = $count > 0 ? ($dayTrades->where('pnl', '>', 0)->count() / $count) * 100 : 0;
        }

        // Sessions (Simplified)
        // Asian: 00-08, London: 08-16, NY: 13-22 (Overlap handled simply by start time)
        $sessions = ['Asian' => [0, 8], 'London' => [8, 13], 'NY' => [13, 24]];
        $winRateBySession = [];
        foreach ($sessions as $name => $range) {
            $sessionTrades = $statsJournals->filter(function ($t) use ($range) {
                $h = $t->open_date->hour;
                return $h >= $range[0] && $h < $range[1];
            });
            $count = $sessionTrades->count();
            $winRateBySession[$name] = $count > 0 ? ($sessionTrades->where('pnl', '>', 0)->count() / $count) * 100 : 0;
        }

        // --- Advanced Analytics (Long vs Short) ---
        $longTrades = $statsJournals->where('type', 'buy');
        $shortTrades = $statsJournals->where('type', 'sell');

        $longWinRate = $longTrades->count() > 0 ? ($longTrades->where('pnl', '>', 0)->count() / $longTrades->count()) * 100 : 0;
        $shortWinRate = $shortTrades->count() > 0 ? ($shortTrades->where('pnl', '>', 0)->count() / $shortTrades->count()) * 100 : 0;

        $longPnL = $longTrades->sum('pnl');
        $shortPnL = $shortTrades->sum('pnl');
        $longCount = $longTrades->count();
        $shortCount = $shortTrades->count();

        // --- Advanced Analytics (Hourly) ---
        $hourlyStats = $statsJournals->groupBy(function ($trade) {
            return $trade->open_date->hour;
        })->map(function ($trades, $hour) {
            $count = $trades->count();
            $winRate = $count > 0 ? ($trades->where('pnl', '>', 0)->count() / $count) * 100 : 0;
            return [
                'hour' => $hour,
                'count' => $count,
                'pnl' => $trades->sum('pnl'),
                'win_rate' => $winRate,
            ];
        })->sortBy('hour');

        // Get all unique pairs for the filter dropdown
        $pairs = auth()->user()->tradingJournals()->select('pair')->distinct()->pluck('pair');

        // Get Accounts
        $accounts = auth()->user()->tradingAccounts;

        // Get Currency Rate
        $currencyRate = $currencyService->getRate('USD', 'IDR');

        // Heatmap Data is already calculated above using $closedTrades (Last 365 Days)
        // We do NOT want to overwrite it with $journals (paginated data)


        return view('journal.index', compact(
            'journals',
            'goal',
            'winRate',
            'profitFactor',
            'totalPnL',
            'equityDates',
            'equityValues',
            'equityMeta',
            'winRateByDay',
            'winRateBySession',
            'longWinRate',
            'shortWinRate',
            'longPnL',
            'shortPnL',
            'longCount',
            'shortCount',
            'hourlyStats',
            'pairs',
            'accounts',
            'currencyRate',
            'heatmapData'
        ));
    }

    public function create()
    {
        $accounts = auth()->user()->tradingAccounts;
        $strategies = auth()->user()->tradingStrategies;
        return view('journal.create', compact('accounts', 'strategies'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'nullable|exists:trading_accounts,id',
            'strategy_id' => 'nullable|exists:trading_strategies,id',
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'lot_size' => 'required|numeric',
            'pnl' => 'nullable|numeric',
            'pips' => 'nullable|numeric',
            'commission' => 'nullable|numeric',
            'swap' => 'nullable|numeric',
            'status' => 'required|in:open,closed,breakeven',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'emotion' => 'required|in:neutral,fomo,revenge,confident,fearful,greedy',
            'strategy' => 'nullable|string', // Keep for legacy string support if needed, or remove if fully migrated
            'tags' => 'nullable|string',
        ]);

        $validated['pair'] = strtoupper($validated['pair']);
        $validated['tags'] = !empty($validated['tags']) ? array_map('trim', explode(',', $validated['tags'])) : [];
        $validated['commission'] = $validated['commission'] ?? 0;
        $validated['swap'] = $validated['swap'] ?? 0;

        if (($validated['status'] === 'closed' || $validated['status'] === 'breakeven') && empty($validated['close_date'])) {
            $validated['close_date'] = $validated['open_date'];
        }

        $journal = auth()->user()->tradingJournals()->create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('journal-screenshots', 'public');
                $journal->images()->create([
                    'image_path' => $path,
                    'type' => 'analysis'
                ]);

                // Set first image as legacy screenshot
                if ($index === 0) {
                    $journal->update(['screenshot' => $path]);
                }
            }
        }

        return redirect()->route('journal.index')->with('success', 'Trade recorded successfully.');
    }

    // ... show ... create ...
    public function show(TradingJournal $journal)
    {
        if ($journal->user_id !== auth()->id()) {
            abort(403);
        }

        $journal->load('tradingStrategy');

        return response()->json($journal);
    }

    public function edit(TradingJournal $journal)
    {
        Gate::authorize('update', $journal);
        $accounts = auth()->user()->tradingAccounts;
        $strategies = auth()->user()->tradingStrategies;
        return view('journal.edit', compact('journal', 'accounts', 'strategies'));
    }

    public function update(Request $request, TradingJournal $journal)
    {
        Gate::authorize('update', $journal);

        $validated = $request->validate([
            'account_id' => 'nullable|exists:trading_accounts,id',
            'strategy_id' => 'nullable|exists:trading_strategies,id',
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell', // ... abbreviated common fields ...
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'lot_size' => 'required|numeric',
            'pnl' => 'nullable|numeric',
            'pips' => 'nullable|numeric',
            'commission' => 'nullable|numeric',
            'swap' => 'nullable|numeric',
            'status' => 'required|in:open,closed,breakeven',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'emotion' => 'required|in:neutral,fomo,revenge,confident,fearful,greedy',
            'strategy' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $validated['pair'] = strtoupper($validated['pair']);
        $validated['tags'] = !empty($validated['tags']) ? array_map('trim', explode(',', $validated['tags'])) : [];
        $validated['commission'] = $validated['commission'] ?? 0;
        $validated['swap'] = $validated['swap'] ?? 0;

        if (($validated['status'] === 'closed' || $validated['status'] === 'breakeven') && empty($validated['close_date'])) {
            $validated['close_date'] = $validated['open_date'];
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('journal-screenshots', 'public');
                $journal->images()->create([
                    'image_path' => $path,
                    'type' => 'analysis'
                ]);

                // Update legacy screenshot if empty or if it's the first upload and we want to overwrite?
                // Let's keep it simple: if legacy is empty, fill it.
                if (!$journal->screenshot && $index === 0) {
                    $validated['screenshot'] = $path;
                }
            }
        }

        $journal->update($validated);

        return redirect()->route('journal.index')->with('success', 'Trade updated successfully.');
    }

    public function destroy(TradingJournal $journal)
    {
        Gate::authorize('delete', $journal);

        if ($journal->screenshot) {
            Storage::disk('public')->delete($journal->screenshot);
        }

        $journal->delete();

        return redirect()->route('journal.index')->with('success', 'Trade deleted successfully.');
    }

    public function setGoal(Request $request)
    {
        $validated = $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        auth()->user()->tradingGoals()->updateOrCreate(
            [
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'target_amount' => $validated['target_amount'],
            ]
        );

        return redirect()->back()->with('success', 'Monthly goal updated.');
    }

    public function deleteImage(\App\Models\JournalImage $image)
    {
        // Verify Ownership
        if ($image->tradingJournal->user_id !== auth()->id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
}
