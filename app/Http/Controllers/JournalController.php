<?php

namespace App\Http\Controllers;

use App\Models\TradingJournal;
use App\Models\TradingGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->tradingJournals()->latest('open_date');

        // Apply Filters
        if ($request->filled('date')) {
            $query->whereDate('open_date', $request->date);
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

        $journals = $query->get();
        $goal = auth()->user()->tradingGoals()->where('month', now()->month)->where('year', now()->year)->first();
        
        // Calculate stats (based on filtered journals)
        $totalTrades = $journals->count();
        $winRate = $totalTrades > 0 ? ($journals->where('pnl', '>', 0)->count() / $totalTrades) * 100 : 0;
        $profitFactor = $journals->where('pnl', '<', 0)->sum('pnl') != 0 
            ? abs($journals->where('pnl', '>', 0)->sum('pnl') / $journals->where('pnl', '<', 0)->sum('pnl')) 
            : 0;
        $totalPnL = $journals->sum('pnl');

        // Equity Curve Data (Cumulative PnL over time) - ALWAYS GLOBAL (Unfiltered)
        $closedTrades = auth()->user()->tradingJournals()
            ->where('status', '!=', 'open')
            ->get()
            ->sortBy(function($trade) {
                return $trade->close_date ?? $trade->open_date;
            });

        $dailyPnL = $closedTrades->groupBy(function ($trade) {
            return ($trade->close_date ?? $trade->open_date)->format('Y-m-d');
        })->map(function ($dayTrades) {
            return $dayTrades->sum('pnl');
        });

        $equityDates = [];
        $equityValues = [];
        $cumulativePnL = 0;

        // Add initial point (Start of data or 0)
        if ($dailyPnL->isNotEmpty()) {
            $firstDate = \Carbon\Carbon::parse($dailyPnL->keys()->first())->subDay();
            $equityDates[] = $firstDate->format('d M');
            $equityValues[] = 0;
        }

        foreach ($dailyPnL as $date => $pnl) {
            $cumulativePnL += $pnl;
            $equityDates[] = \Carbon\Carbon::parse($date)->format('d M');
            $equityValues[] = $cumulativePnL;
        }
        
        // Get all unique pairs for the filter dropdown
        $pairs = auth()->user()->tradingJournals()->select('pair')->distinct()->pluck('pair');

        return view('journal.index', compact('journals', 'goal', 'winRate', 'profitFactor', 'totalPnL', 'equityDates', 'equityValues', 'pairs'));
    }

    public function create()
    {
        return view('journal.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'lot_size' => 'required|numeric',
            'pnl' => 'nullable|numeric',
            'pips' => 'nullable|numeric',
            'status' => 'required|in:open,closed,breakeven',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'screenshot' => 'nullable|image|max:2048',
            'emotion' => 'required|in:neutral,fomo,revenge,confident,fearful,greedy',
            'strategy' => 'nullable|string',
        ]);

        // Normalize Pair to Uppercase
        $validated['pair'] = strtoupper($validated['pair']);

        // Ensure close_date is set if trade is closed
        if (($validated['status'] === 'closed' || $validated['status'] === 'breakeven') && empty($validated['close_date'])) {
            $validated['close_date'] = $validated['open_date'];
        }

        if ($request->hasFile('screenshot')) {
            $validated['screenshot'] = $request->file('screenshot')->store('journal-screenshots', 'public');
        }

        auth()->user()->tradingJournals()->create($validated);

        return redirect()->route('journal.index')->with('success', 'Trade recorded successfully.');
    }

    public function show(TradingJournal $journal)
    {
        Gate::authorize('view', $journal);
        
        return response()->json([
            'id' => $journal->id,
            'pair' => $journal->pair,
            'type' => $journal->type,
            'entry_price' => $journal->entry_price,
            'exit_price' => $journal->exit_price,
            'lot_size' => $journal->lot_size,
            'pnl' => $journal->pnl,
            'pips' => $journal->pips,
            'status' => $journal->status,
            'open_date' => $journal->open_date->format('d M Y H:i'),
            'close_date' => $journal->close_date ? $journal->close_date->format('d M Y H:i') : '-',
            'notes' => $journal->notes,
            'screenshot_url' => $journal->screenshot ? Storage::url($journal->screenshot) : null,
            'emotion' => $journal->emotion,
            'strategy' => $journal->strategy,
        ]);
    }

    public function edit(TradingJournal $journal)
    {
        Gate::authorize('update', $journal);
        return view('journal.edit', compact('journal'));
    }

    public function export(Request $request)
    {
        $query = auth()->user()->tradingJournals()->latest('open_date');

        // Apply Filters (Same as index)
        if ($request->filled('date')) {
            $query->whereDate('open_date', $request->date);
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

        $journals = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=trading_journal_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Open Date', 'Close Date', 'Pair', 'Type', 'Entry Price', 'Exit Price', 'Lot Size', 'PnL ($)', 'Pips', 'Status', 'Emotion', 'Strategy', 'Notes'];

        $callback = function() use ($journals, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            // Use semicolon (;) as delimiter for better Excel compatibility in some regions
            fputcsv($file, $columns, ';');

            foreach ($journals as $journal) {
                fputcsv($file, [
                    $journal->open_date->format('Y-m-d H:i'),
                    $journal->close_date ? $journal->close_date->format('Y-m-d H:i') : '-',
                    strtoupper($journal->pair),
                    ucfirst($journal->type),
                    $journal->entry_price,
                    $journal->exit_price,
                    $journal->lot_size,
                    $journal->pnl,
                    $journal->pips,
                    ucfirst($journal->status),
                    ucfirst($journal->emotion),
                    $journal->strategy,
                    $journal->notes
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function update(Request $request, TradingJournal $journal)
    {
        Gate::authorize('update', $journal);

        $validated = $request->validate([
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'lot_size' => 'required|numeric',
            'pnl' => 'nullable|numeric',
            'pips' => 'nullable|numeric',
            'status' => 'required|in:open,closed,breakeven',
            'open_date' => 'required|date',
            'close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'screenshot' => 'nullable|image|max:2048',
            'emotion' => 'required|in:neutral,fomo,revenge,confident,fearful,greedy',
            'strategy' => 'nullable|string',
        ]);

        // Normalize Pair to Uppercase
        $validated['pair'] = strtoupper($validated['pair']);

        // Ensure close_date is set if trade is closed
        if (($validated['status'] === 'closed' || $validated['status'] === 'breakeven') && empty($validated['close_date'])) {
            $validated['close_date'] = $validated['open_date'];
        }

        if ($request->hasFile('screenshot')) {
            if ($journal->screenshot) {
                Storage::disk('public')->delete($journal->screenshot);
            }
            $validated['screenshot'] = $request->file('screenshot')->store('journal-screenshots', 'public');
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
}
