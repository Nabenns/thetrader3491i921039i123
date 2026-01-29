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

        // Fetch Trades for "TheTrader" (User ID 1) to display stats on Dashboard
        $userId = 1;
        $trades = \App\Models\TradingJournal::where('user_id', $userId)
            ->where('status', 'closed')
            ->orderBy('close_date', 'desc')
            ->get();

        // 1. Equity Curve Data
        $equityDates = [];
        $equityValues = [];
        $cumulativePnL = 0;
        $sortedTrades = $trades->sortBy('close_date');
        
        if ($sortedTrades->isNotEmpty()) {
            $equityDates[] = $sortedTrades->first()->close_date->subDay()->format('d M');
            $equityValues[] = 0;
        }

        foreach ($sortedTrades as $trade) {
            $cumulativePnL += $trade->pnl;
            $equityDates[] = $trade->close_date->format('d M');
            $equityValues[] = $cumulativePnL;
        }

        // 2. Best Trade
        $bestTrade = $trades->sortByDesc('pnl')->first();

        // 3. Heatmap Data (Current Month) - Keep this for compatibility if needed, but we are replacing the widget
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;
        
        $monthlyTrades = auth()->user()->tradingJournals()
            ->whereYear('close_date', $currentYear)
            ->whereMonth('close_date', $currentMonth)
            ->where('status', '!=', 'open')
            ->get();

        $heatmapData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = now()->setDate($currentYear, $currentMonth, $day)->format('Y-m-d');
            $pnl = $monthlyTrades->filter(function ($trade) use ($date) {
                return $trade->close_date && $trade->close_date->format('Y-m-d') === $date;
            })->sum('pnl');
            $heatmapData[$day] = $pnl;
        }

        return view('dashboard', compact(
            'latestMarketWebinar', 
            'heatmapData', 
            'equityDates', 
            'equityValues', 
            'bestTrade'
        ));
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->latest()->paginate(10);
        return view('dashboard.transactions', compact('transactions'));
    }

    public function record()
    {
        // Assuming "TheTrader" is User ID 1
        $userId = 1;
        
        $trades = \App\Models\TradingJournal::where('user_id', $userId)
            ->where('status', 'closed')
            ->orderBy('close_date', 'desc')
            ->get();

        // Basic Stats
        $today = now()->format('Y-m-d');
        $pipsToday = $trades->where('close_date', '>=', $today . ' 00:00:00')->sum('pips');
        $profitToday = $trades->where('close_date', '>=', $today . ' 00:00:00')->sum('pnl');
        
        $totalTrades = $trades->count();
        $winRate = $totalTrades > 0 ? ($trades->where('pnl', '>', 0)->count() / $totalTrades) * 100 : 0;

        // 1. Equity Curve
        $equityDates = [];
        $equityValues = [];
        $cumulativePnL = 0;
        
        // Sort by date ascending for the curve
        $sortedTrades = $trades->sortBy('close_date');
        
        if ($sortedTrades->isNotEmpty()) {
            // Add start point
            $equityDates[] = $sortedTrades->first()->close_date->subDay()->format('d M');
            $equityValues[] = 0;
        }

        foreach ($sortedTrades as $trade) {
            $cumulativePnL += $trade->pnl;
            $equityDates[] = $trade->close_date->format('d M');
            $equityValues[] = $cumulativePnL;
        }

        // 2. Monthly Performance
        $monthlyPerformance = $trades->groupBy(function($trade) {
            return $trade->close_date->format('M Y');
        })->map(function($monthTrades) {
            return $monthTrades->sum('pnl');
        });

        // 3. Pair Distribution
        $pairDistribution = $trades->groupBy('pair')->map->count();

        // 4. Heatmap Data (Daily PnL)
        $heatmapData = $trades->groupBy(function($trade) {
            return $trade->close_date->format('Y-m-d');
        })->map(function($dayTrades) {
            return $dayTrades->sum('pnl');
        });

        // 5. Profit Factor
        $grossProfit = $trades->where('pnl', '>', 0)->sum('pnl');
        $grossLoss = abs($trades->where('pnl', '<', 0)->sum('pnl'));
        $profitFactor = $grossLoss > 0 ? $grossProfit / $grossLoss : ($grossProfit > 0 ? 999 : 0);

        // 6. Avg Risk:Reward (Avg Win / Avg Loss)
        $avgWin = $trades->where('pnl', '>', 0)->avg('pnl') ?? 0;
        $avgLoss = abs($trades->where('pnl', '<', 0)->avg('pnl') ?? 0);
        $avgRR = $avgLoss > 0 ? $avgWin / $avgLoss : 0;

        // 7. Max Drawdown
        $maxDrawdown = 0;
        $peak = 0;
        $currentEquity = 0;
        
        foreach ($sortedTrades as $trade) {
            $currentEquity += $trade->pnl;
            if ($currentEquity > $peak) {
                $peak = $currentEquity;
            }
            $drawdown = $peak - $currentEquity;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }

        // 8. Best Trade
        $bestTrade = $trades->sortByDesc('pnl')->first();

        return view('dashboard.record', compact(
            'trades', 
            'pipsToday', 
            'profitToday', 
            'winRate',
            'equityDates',
            'equityValues',
            'monthlyPerformance',
            'pairDistribution',
            'heatmapData',
            'profitFactor',
            'avgRR',
            'maxDrawdown',
            'bestTrade'
        ));
    }
}
