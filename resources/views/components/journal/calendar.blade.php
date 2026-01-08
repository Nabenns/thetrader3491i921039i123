@props(['journals'])

@php
    $daysInMonth = now()->daysInMonth;
    $currentMonth = now()->month;
    $currentYear = now()->year;
    $firstDayOfWeek = now()->startOfMonth()->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
    
    // Adjust for Monday start if needed, currently Sunday start
    
    $calendarData = [];
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
        $dayJournals = $journals->filter(function ($journal) use ($date) {
            return $journal->open_date->isSameDay($date);
        });
        
        $dailyPnL = $dayJournals->sum('pnl');
        $status = 'neutral';
        if ($dayJournals->count() > 0) {
            $status = $dailyPnL > 0 ? 'profit' : ($dailyPnL < 0 ? 'loss' : 'breakeven');
        }
        
        $calendarData[$day] = [
            'date' => $date,
            'pnl' => $dailyPnL,
            'status' => $status,
            'count' => $dayJournals->count(),
        ];
    }
@endphp

<div class="glass rounded-2xl border border-white/10 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-white">Trading Calendar</h3>
        <div class="flex items-center gap-2 text-sm">
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-green-500"></span> Profit</div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Loss</div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-gray-500"></span> No Trade</div>
        </div>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center mb-2">
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
            <div class="text-xs text-gray-500 font-medium">{{ $dayName }}</div>
        @endforeach
    </div>

    <div class="grid grid-cols-7 gap-2">
        {{-- Empty cells for days before start of month --}}
        @for ($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="aspect-square rounded-lg bg-white/5 opacity-50"></div>
        @endfor

        @foreach ($calendarData as $day => $data)
            @php
                $bgColor = 'bg-white/5 hover:bg-white/10';
                $textColor = 'text-gray-400';
                
                if ($data['status'] === 'profit') {
                    $bgColor = 'bg-green-500/20 border border-green-500/30 hover:bg-green-500/30';
                    $textColor = 'text-green-400';
                } elseif ($data['status'] === 'loss') {
                    $bgColor = 'bg-red-500/20 border border-red-500/30 hover:bg-red-500/30';
                    $textColor = 'text-red-400';
                } elseif ($data['status'] === 'breakeven') {
                    $bgColor = 'bg-yellow-500/20 border border-yellow-500/30 hover:bg-yellow-500/30';
                    $textColor = 'text-yellow-400';
                }
            @endphp
            
            <div class="{{ $bgColor }} aspect-square rounded-lg p-1 flex flex-col items-center justify-center transition-all cursor-pointer group relative">
                <span class="text-xs font-medium {{ $textColor }}">{{ $day }}</span>
                @if($data['count'] > 0)
                    <span class="text-[10px] {{ $textColor }} font-bold mt-1">
                        ${{ number_format(abs($data['pnl']), 0) }}
                    </span>
                @endif
                
                {{-- Tooltip --}}
                @if($data['count'] > 0)
                    <div class="absolute bottom-full mb-2 hidden group-hover:block z-10 w-max">
                        <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 border border-white/10 shadow-xl">
                            {{ $data['count'] }} Trades<br>
                            PnL: ${{ number_format($data['pnl'], 2) }}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
