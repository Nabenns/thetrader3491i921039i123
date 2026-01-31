@props(['data', 'year' => null])

@php
    $year = $year ?? now()->year;
    $startDate = \Carbon\Carbon::createFromDate($year, 1, 1);
    $endDate = \Carbon\Carbon::createFromDate($year, 12, 31);
    
    // Generate all days in the year
    $days = [];
    $current = $startDate->copy();
    
    while ($current->lte($endDate)) {
        $dateStr = $current->format('Y-m-d');
        $dayData = $data[$dateStr] ?? null;
        
        $colorClass = 'bg-gray-800/50'; // Default empty
        
        if ($dayData) {
            if ($dayData['pnl'] > 0) {
                // Green Intensity
                if ($dayData['pnl'] > 500) $colorClass = 'bg-green-500';
                elseif ($dayData['pnl'] > 200) $colorClass = 'bg-green-600';
                elseif ($dayData['pnl'] > 100) $colorClass = 'bg-green-700';
                else $colorClass = 'bg-green-800';
            } elseif ($dayData['pnl'] < 0) {
                // Red Intensity
                if ($dayData['pnl'] < -500) $colorClass = 'bg-red-500';
                elseif ($dayData['pnl'] < -200) $colorClass = 'bg-red-600';
                elseif ($dayData['pnl'] < -100) $colorClass = 'bg-red-700';
                else $colorClass = 'bg-red-800';
            } else {
                // Break Even / Activity only
                $colorClass = 'bg-gray-600';
            }
        }
        
        $days[] = [
            'date' => $dateStr,
            'day_of_week' => $current->dayOfWeek, // 0 (Sun) - 6 (Sat)
            'month' => $current->month,
            'data' => $dayData,
            'color' => $colorClass,
            'formatted_date' => $current->format('d M Y'),
        ];
        
        $current->addDay();
    }
    
    // Group by weeks for the grid
    $weeks = [];
    $currentWeek = [];
    
    // Pad the first week if it doesn't start on Sunday
    $firstDayOfWeek = $startDate->dayOfWeek;
    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $currentWeek[] = null; // Empty placeholder
    }
    
    foreach ($days as $day) {
        $currentWeek[] = $day;
        
        if (count($currentWeek) === 7) {
            $weeks[] = $currentWeek;
            $currentWeek = [];
        }
    }
    
    // Add remaining days
    if (!empty($currentWeek)) {
        while (count($currentWeek) < 7) {
            $currentWeek[] = null;
        }
        $weeks[] = $currentWeek;
    }
    
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
@endphp

<div class="glass-card p-6 rounded-2xl overflow-x-auto">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-white">Trading Activity</h3>
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <span>Less</span>
            <div class="w-3 h-3 rounded-sm bg-gray-800/50"></div>
            <div class="w-3 h-3 rounded-sm bg-green-900"></div>
            <div class="w-3 h-3 rounded-sm bg-green-700"></div>
            <div class="w-3 h-3 rounded-sm bg-green-500"></div>
            <span>More</span>
        </div>
    </div>

    <div class="min-w-[800px]">
        <!-- Month Labels -->
        <div class="flex mb-2 pl-8">
            @foreach($months as $index => $month)
                <div class="flex-1 text-xs text-gray-500">{{ $month }}</div>
            @endforeach
        </div>

        <div class="flex gap-1">
            <!-- Day Labels -->
            <div class="flex flex-col justify-between text-[10px] text-gray-500 pr-2 py-1 h-[100px]">
                <span>Mon</span>
                <span>Wed</span>
                <span>Fri</span>
            </div>

            <!-- The Grid -->
            <div class="flex gap-1 flex-1">
                @foreach($weeks as $week)
                    <div class="flex flex-col gap-1">
                        @foreach($week as $day)
                            @if($day)
                                <div 
                                    class="w-3 h-3 rounded-sm {{ $day['color'] }} transition-all hover:scale-125 hover:z-10 relative group cursor-pointer"
                                    title="{{ $day['formatted_date'] }}"
                                >
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-20 w-48 p-2 bg-gray-900 text-xs text-gray-300 rounded-lg border border-white/10 shadow-xl pointer-events-none">
                                        <div class="font-bold text-white mb-1">{{ $day['formatted_date'] }}</div>
                                        @if($day['data'])
                                            <div class="flex justify-between">
                                                <span>PnL:</span>
                                                <span class="{{ $day['data']['pnl'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $day['data']['pnl'] >= 0 ? '+' : '' }}${{ number_format($day['data']['pnl'], 2) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Trades:</span>
                                                <span class="text-white">{{ $day['data']['count'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Win Rate:</span>
                                                <span class="text-blue-400">{{ number_format($day['data']['win_rate'], 0) }}%</span>
                                            </div>
                                        @else
                                            <div class="text-gray-500 italic">No trades</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="w-3 h-3"></div> <!-- Placeholder -->
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
