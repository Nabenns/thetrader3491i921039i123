@props(['data'])

<div class="glass-card p-6 rounded-2xl overflow-x-auto">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-white">Trading Activity (Last 365 Days)</h3>
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <span>Less</span>
            <span class="w-3 h-3 rounded-[2px] bg-white/5"></span>
            <span class="w-3 h-3 rounded-[2px] bg-teal-500/20"></span>
            <span class="w-3 h-3 rounded-[2px] bg-teal-500/50"></span>
            <span class="w-3 h-3 rounded-[2px] bg-teal-500"></span>
            <span>More</span>
        </div>
    </div>

    @php
        // Generate last 365 days dates
        $endDate = now();
        $startDate = now()->subDays(364);
        $dates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dates[$current->format('Y-m-d')] = [
                'date' => $current->copy(),
                'data' => $data[$current->format('Y-m-d')] ?? null
            ];
            $current->addDay();
        }

        // Group by weeks for columns
        $weeks = [];
        foreach ($dates as $dateStr => $info) {
            $weekYear = $info['date']->year;
            $weekNum = $info['date']->weekOfYear;
            // Key by year-week to handle crossover
            $key = $weekYear . '-' . $weekNum;
            $weeks[$key][] = $info;
        }
    @endphp

    <div class="flex gap-1 min-w-max">
        <!-- Day Labels -->
        <div class="flex flex-col gap-1 pr-2 pt-5">
            <span class="text-[10px] text-gray-500 h-3">Mon</span>
            <span class="text-[10px] text-gray-500 h-3 mt-3">Wed</span>
            <span class="text-[10px] text-gray-500 h-3 mt-3">Fri</span>
        </div>

        <!-- Heatmap Grid -->
        <div class="flex gap-1">
            @foreach($weeks as $weekDates)
                <div class="flex flex-col gap-1">
                    @foreach($weekDates as $info)
                        @php
                            $pnl = $info['data']['pnl'] ?? 0;
                            $count = $info['data']['count'] ?? 0;

                            $colorClass = 'bg-white/5'; // Default empty
                            if ($count > 0) {
                                if ($pnl > 0) {
                                    // Profit intensity
                                    $colorClass = $pnl > 500 ? 'bg-green-400' : ($pnl > 100 ? 'bg-green-500' : 'bg-green-600');
                                } elseif ($pnl < 0) {
                                    // Loss intensity
                                    $colorClass = $pnl < -500 ? 'bg-red-500' : ($pnl < -100 ? 'bg-red-600' : 'bg-red-700/50');
                                } else {
                                    // Break even
                                    $colorClass = 'bg-gray-500';
                                }
                            }
                        @endphp

                        <div class="w-3 h-3 rounded-[2px] {{ $colorClass }} group relative cursor-pointer hover:ring-1 hover:ring-white/50 transition-all"
                            @if($count > 0)
                                title="{{ $info['date']->format('d M Y') }}: {{ $count }} trades, {{ $pnl >= 0 ? '+' : '' }}{{ number_format($pnl, 2) }}"
                            @else title="{{ $info['date']->format('d M Y') }}: No trades" @endif>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>