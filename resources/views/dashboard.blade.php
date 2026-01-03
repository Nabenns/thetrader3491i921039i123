<x-dashboard-layout>
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Dashboard</h2>
                <p class="text-gray-400 mt-1">Welcome back, <span class="text-white font-semibold">{{ Auth::user()->name }}</span>! Here's your market summary.</p>
            </div>
            <a href="/#pricing" class="group relative px-6 py-2.5 bg-gradient-to-r from-primary to-secondary rounded-xl font-semibold text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-500 ease-out -skew-x-12 -translate-x-full"></div>
                <span class="relative flex items-center gap-2">
                    Upgrade Premium
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </span>
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat 1 -->
            <div class="glass p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-primary/30 transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Status Langganan</p>
                        @php
                            $activeSubscription = Auth::user()->subscriptions()
                                ->where('status', 'active')
                                ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
                                ->with('package')
                                ->latest()
                                ->first();
                        @endphp
                        
                        @if($activeSubscription)
                            <h3 class="text-2xl font-bold text-white mt-1">{{ $activeSubscription->package->name }}</h3>
                        @else
                            <h3 class="text-2xl font-bold text-white mt-1">Free Member</h3>
                        @endif
                    </div>
                    <div class="p-3 bg-primary/10 rounded-xl text-primary group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                
                @if($activeSubscription)
                    <div class="flex items-center text-xs text-green-400 bg-green-400/10 px-2 py-1 rounded-lg w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                        Aktif
                        @if($activeSubscription->ends_at)
                             • Berakhir {{ $activeSubscription->ends_at->format('d M Y') }}
                        @else
                             • Lifetime
                        @endif
                    </div>
                @else
                    <div class="flex items-center text-xs text-yellow-500 bg-yellow-500/10 px-2 py-1 rounded-lg w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-2 animate-pulse"></span>
                        Belum aktif
                    </div>
                @endif
            </div>

            <!-- Stat 2 -->
            <div class="glass p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-secondary/30 transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Webinar Berikutnya</p>
                        @if($nextWebinar)
                            @php
                                $daysLeft = now()->diffInDays($nextWebinar->schedule, false);
                                $hoursLeft = now()->diffInHours($nextWebinar->schedule, false);
                            @endphp
                            
                            @if($daysLeft > 0)
                                <h3 class="text-2xl font-bold text-white mt-1">{{ $daysLeft }} Hari Lagi</h3>
                            @elseif($hoursLeft > 0)
                                <h3 class="text-2xl font-bold text-white mt-1">{{ $hoursLeft }} Jam Lagi</h3>
                            @else
                                <h3 class="text-2xl font-bold text-green-400 mt-1">Sedang Berlangsung</h3>
                            @endif
                        @else
                            <h3 class="text-xl font-bold text-white mt-1">Belum Ada</h3>
                        @endif
                    </div>
                    <div class="p-3 bg-secondary/10 rounded-xl text-secondary group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="flex items-center text-xs text-gray-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    @if($nextWebinar)
                        {{ $nextWebinar->schedule->format('l, H:i') }} WIB
                    @else
                        -
                    @endif
                </div>
            </div>

            <!-- Stat 3 -->
            <div x-data="{ rate: 'Loading...', loading: true }" 
                 x-init="fetch('https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_1s6bktzu6WoZvQFu6sRVRFimM1Q1IGuGrBaa1036&currencies=IDR')
                    .then(res => res.json())
                    .then(data => { 
                        rate = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(data.data.IDR); 
                        loading = false;
                    })" 
                 class="glass p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-blue-500/30 transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Kurs USD/IDR</p>
                        <h3 class="text-2xl font-bold text-white mt-1" x-text="rate">
                            <span class="animate-pulse bg-white/10 h-8 w-32 rounded block"></span>
                        </h3>
                    </div>
                    <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Stat 4 -->
            <div x-data="{ 
                    session: 'Loading...', 
                    status: 'Closed',
                    updateSession() {
                        const hour = new Date().getUTCHours();
                        if (hour >= 21 || hour < 6) { this.session = 'Sydney Session'; this.status = 'Open'; }
                        else if (hour >= 0 && hour < 9) { this.session = 'Tokyo Session'; this.status = 'Open'; }
                        else if (hour >= 7 && hour < 16) { this.session = 'London Session'; this.status = 'Open'; }
                        else if (hour >= 12 && hour < 21) { this.session = 'New York Session'; this.status = 'Open'; }
                        else { this.session = 'Market Closed'; this.status = 'Closed'; }
                        
                        // Overlaps
                        if (hour >= 7 && hour < 9) this.session = 'London & Tokyo';
                        if (hour >= 12 && hour < 16) this.session = 'London & New York';
                    }
                 }" 
                 x-init="updateSession(); setInterval(() => updateSession(), 60000)"
                 class="glass p-6 rounded-2xl border border-white/10 relative overflow-hidden group hover:border-purple-500/30 transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Sesi Pasar</p>
                        <h3 class="text-xl font-bold text-white mt-1" x-text="session">Loading...</h3>
                    </div>
                    <div class="p-3 bg-purple-500/10 rounded-xl text-purple-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-center text-xs">
                    <span class="relative flex h-2 w-2 mr-2">
                      <span x-show="status === 'Open'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span :class="status === 'Open' ? 'bg-green-500' : 'bg-red-500'" class="relative inline-flex rounded-full h-2 w-2"></span>
                    </span>
                    <span :class="status === 'Open' ? 'text-green-400' : 'text-red-400'" x-text="status"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Chart / Market Overview -->
            <div class="lg:col-span-2 glass rounded-2xl border border-white/10 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <div class="w-1 h-6 bg-primary rounded-full"></div>
                        Market Overview
                    </h3>
                </div>
                
                <!-- TradingView Widget Container -->
                <div class="tradingview-widget-container flex-1 w-full min-h-[400px] bg-black/20">
                    <div class="tradingview-widget-container__widget"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js" async>
                    {
                    "symbols": [
                        [
                        "OANDA:XAUUSD|1D"
                        ],
                        [
                        "BITSTAMP:BTCUSD|1D"
                        ],
                        [
                        "FX:EURUSD|1D"
                        ]
                    ],
                    "chartOnly": false,
                    "width": "100%",
                    "height": "100%",
                    "locale": "en",
                    "colorTheme": "dark",
                    "autosize": true,
                    "showVolume": false,
                    "showMA": false,
                    "hideDateRanges": true,
                    "hideMarketStatus": false,
                    "hideSymbolLogo": false,
                    "scalePosition": "right",
                    "scaleMode": "Normal",
                    "fontFamily": "Inter, sans-serif",
                    "fontSize": "10",
                    "noTimeScale": false,
                    "valuesTracking": "1",
                    "changeMode": "price-and-percent",
                    "chartType": "area",
                    "maLineColor": "#2962FF",
                    "maLineWidth": 1,
                    "maLength": 9,
                    "backgroundColor": "rgba(0, 0, 0, 0)",
                    "lineWidth": 2,
                    "lineType": 0,
                    "dateRanges": [
                        "1d|1",
                        "1m|30",
                        "3m|60",
                        "12m|1D",
                        "60m|1W",
                        "all|1M"
                    ]
                    }
                    </script>
                </div>
            </div>

            <!-- Economic Calendar -->
            <div class="glass rounded-2xl border border-white/10 overflow-hidden flex flex-col h-[500px]">
                <div class="p-6 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <div class="w-1 h-6 bg-secondary rounded-full"></div>
                        Kalender Ekonomi
                    </h3>
                </div>
                
                <div class="flex-1 bg-black/20">
                    <!-- TradingView Widget BEGIN -->
                    <div class="tradingview-widget-container h-full w-full">
                        <div class="tradingview-widget-container__widget"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-events.js" async>
                        {
                        "colorTheme": "dark",
                        "isTransparent": true,
                        "width": "100%",
                        "height": "100%",
                        "locale": "id",
                        "importanceFilter": "0,1",
                        "currencyFilter": "USD,EUR,GBP,JPY,AUD,CAD,CHF,NZD,CNY"
                        }
                        </script>
                    </div>
                    <!-- TradingView Widget END -->
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
