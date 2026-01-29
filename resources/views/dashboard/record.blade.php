<x-dashboard-layout>
    <div class="space-y-8" x-data="{ tradeModalOpen: false, trade: null, loading: false }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 gsap-header">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Our Record</h2>
                <p class="text-gray-400 mt-1">
                    Transparansi performa trading <span class="text-primary font-semibold">TheTrader</span>.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-400 text-xs font-bold border border-green-500/20 animate-pulse flex items-center gap-2">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Symbols/Green%20Circle.png" alt="Live" class="w-4 h-4" />
                    Live Account
                </span>
            </div>
        </div>

        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Win Rate -->
            <div class="glass p-5 rounded-2xl border border-white/10 relative overflow-hidden gsap-stat-card">
                <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Win Rate</p>
                <h3 class="text-2xl font-bold text-white mt-1">
                    <span class="count-up" data-value="{{ $winRate }}" data-decimals="1" data-suffix="%">0.0%</span>
                </h3>
                <div class="absolute top-4 right-4 p-2 bg-purple-500/10 rounded-lg text-purple-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Profit Factor -->
            <div class="glass p-5 rounded-2xl border border-white/10 relative overflow-hidden gsap-stat-card">
                <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Profit Factor</p>
                <h3 class="text-2xl font-bold text-white mt-1">
                    <span class="count-up" data-value="{{ $profitFactor }}" data-decimals="2">0.00</span>
                </h3>
                <div class="absolute top-4 right-4 p-2 bg-blue-500/10 rounded-lg text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>

            <!-- Avg R:R -->
            <div class="glass p-5 rounded-2xl border border-white/10 relative overflow-hidden gsap-stat-card">
                <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Avg Risk:Reward</p>
                <h3 class="text-2xl font-bold text-white mt-1">
                    1:<span class="count-up" data-value="{{ $avgRR }}" data-decimals="1">0.0</span>
                </h3>
                <div class="absolute top-4 right-4 p-2 bg-orange-500/10 rounded-lg text-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                </div>
            </div>

            <!-- Max Drawdown -->
            <div class="glass p-5 rounded-2xl border border-white/10 relative overflow-hidden gsap-stat-card">
                <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Max Drawdown</p>
                <h3 class="text-2xl font-bold text-red-400 mt-1">
                    -<span class="count-up" data-value="{{ $maxDrawdown }}" data-decimals="2" data-prefix="$">0.00</span>
                </h3>
                <div class="absolute top-4 right-4 p-2 bg-red-500/10 rounded-lg text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Equity Curve (2/3 width) -->
            <div class="lg:col-span-2 glass p-6 rounded-2xl border border-white/10 gsap-chart">
                <h3 class="text-lg font-bold text-white mb-4">Equity Growth</h3>
                <div class="relative h-72 w-full">
                    <canvas id="equityChart"></canvas>
                </div>
            </div>

            <!-- Best Trade Spotlight (1/3 width) -->
            <div class="glass p-6 rounded-2xl border border-white/10 flex flex-col gsap-chart relative overflow-hidden group/shimmer">
                <!-- Shimmer Effect -->
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/5 to-transparent shimmer-effect pointer-events-none z-10"></div>
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Activities/Trophy.png" alt="Trophy" class="w-8 h-8" />
                    Best Trade
                </h3>
                @if($bestTrade)
                    <div class="flex-1 flex flex-col justify-between cursor-pointer group"
                         @click="trade = null; loading = true; tradeModalOpen = true; fetch('/journal/{{ $bestTrade->id }}').then(res => res.json()).then(data => { trade = data; loading = false; })">
                        
                        <div class="relative h-40 rounded-xl overflow-hidden mb-4 border border-white/10 group-hover:border-primary/50 transition-colors">
                            @if($bestTrade->screenshot)
                                <img src="{{ Storage::url($bestTrade->screenshot) }}" class="w-full h-full object-cover opacity-60 group-hover:opacity-80 transition-opacity">
                            @else
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-600">No Chart</div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent p-4 flex flex-col justify-end">
                                <div class="text-2xl font-bold text-green-400">+${{ number_format($bestTrade->pnl, 2) }}</div>
                                <div class="text-sm text-gray-300">{{ $bestTrade->pair }} &bull; {{ $bestTrade->type }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="bg-white/5 p-2 rounded-lg">
                                <span class="text-gray-500 text-xs block">Pips</span>
                                <span class="text-white font-mono">+{{ number_format($bestTrade->pips, 1) }}</span>
                            </div>
                            <div class="bg-white/5 p-2 rounded-lg">
                                <span class="text-gray-500 text-xs block">Date</span>
                                <span class="text-white font-mono">{{ $bestTrade->close_date->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-gray-500">No trades yet</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Performance -->
            <div class="glass p-6 rounded-2xl border border-white/10 gsap-chart">
                <h3 class="text-lg font-bold text-white mb-4">Monthly Performance</h3>
                <div class="relative h-64 w-full">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Pair Distribution -->
            <div class="glass p-6 rounded-2xl border border-white/10 gsap-chart">
                <h3 class="text-lg font-bold text-white mb-4">Pair Distribution</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="pairChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Trade History Table -->
        <div class="glass rounded-2xl border border-white/10 overflow-hidden gsap-table">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Trade History</h3>
                <span class="text-xs text-gray-500">Click row for details</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-white/5 text-xs uppercase font-semibold text-white">
                        <tr>
                            <th class="px-6 py-4">Pair</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Entry</th>
                            <th class="px-6 py-4">Exit</th>
                            <th class="px-6 py-4">Pips</th>
                            <th class="px-6 py-4">PnL</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($trades as $trade)
                            <tr class="hover:bg-white/5 transition-colors cursor-pointer group gsap-row opacity-0"
                                @click="trade = null; loading = true; tradeModalOpen = true; fetch('/journal/{{ $trade->id }}').then(res => res.json()).then(data => { trade = data; loading = false; })">
                                <td class="px-6 py-4 font-medium text-white group-hover:text-primary transition-colors">{{ $trade->pair }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $trade->type === 'buy' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ strtoupper($trade->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ number_format($trade->entry_price, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($trade->exit_price, 2) }}</td>
                                <td class="px-6 py-4 {{ $trade->pips >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trade->pips > 0 ? '+' : '' }}{{ number_format($trade->pips, 1) }}
                                </td>
                                <td class="px-6 py-4 {{ $trade->pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trade->pnl > 0 ? '+' : '' }}${{ number_format($trade->pnl, 2) }}
                                </td>
                                <td class="px-6 py-4">{{ $trade->close_date->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada data trading yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trade Detail Modal (Reused Design) -->
        <div 
            x-show="tradeModalOpen"
            style="display: none;"
            class="fixed inset-0 z-[70] overflow-y-auto" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div 
                    x-show="tradeModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
                    aria-hidden="true" 
                    @click="tradeModalOpen = false"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div 
                    x-show="tradeModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full z-[80]"
                >
                    <div class="p-6" x-show="loading">
                        <div class="flex justify-center items-center py-12">
                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <div x-show="!loading && trade" class="flex flex-col h-full">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-black/20">
                            <div class="flex items-center gap-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-white tracking-tight" x-text="trade?.pair"></h2>
                                    <div class="flex items-center gap-2 text-sm text-gray-400">
                                        <span class="uppercase font-bold" 
                                            :class="trade?.type === 'buy' ? 'text-green-400' : 'text-red-400'"
                                            x-text="trade?.type"></span>
                                        <span>&bull;</span>
                                        <span x-text="trade?.open_date"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold tracking-tight" 
                                    :class="trade?.pnl >= 0 ? 'text-green-400' : 'text-red-400'"
                                    x-text="(trade?.pnl >= 0 ? '+' : '') + '$' + Number(trade?.pnl).toFixed(2)"></div>
                                <div class="text-sm font-medium text-gray-400" x-text="trade?.pips + ' pips'"></div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <!-- Left: Chart (Hero) -->
                            <div class="w-full md:w-2/3 bg-gray-900/50 relative min-h-[300px] md:min-h-[400px] border-r border-white/5">
                                <template x-if="trade?.screenshot_url">
                                    <img :src="trade.screenshot_url" class="absolute inset-0 w-full h-full object-contain bg-black/40">
                                </template>
                                <template x-if="!trade?.screenshot_url">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-600">
                                        <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-sm font-medium">No Chart Screenshot</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Right: Details -->
                            <div class="w-full md:w-1/3 p-6 space-y-6 bg-white/5 backdrop-blur-sm">
                                <!-- Strategy & Emotion -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 rounded-xl bg-black/20 border border-white/5">
                                        <label class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1 block">Strategy</label>
                                        <div class="text-sm font-medium text-white truncate" x-text="trade?.strategy || '-'"></div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-black/20 border border-white/5">
                                        <label class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1 block">Emotion</label>
                                        <div class="flex items-center gap-2">
                                            <span class="capitalize text-sm font-medium text-white" x-text="trade?.emotion"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trade Stats -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Entry Price</span>
                                        <span class="text-sm font-mono font-bold text-white" x-text="trade?.entry_price"></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Exit Price</span>
                                        <span class="text-sm font-mono font-bold text-white" x-text="trade?.exit_price || '-'"></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Lot Size</span>
                                        <span class="text-sm font-mono font-bold text-white" x-text="trade?.lot_size"></span>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="flex-1">
                                    <label class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-2 block">Notes</label>
                                    <div class="p-4 rounded-xl bg-black/20 border border-white/5 min-h-[100px] max-h-[200px] overflow-y-auto text-sm text-gray-300 leading-relaxed" x-text="trade?.notes || 'No notes added.'"></div>
                                </div>

                                <!-- Actions -->
                                <div class="pt-4 mt-auto flex gap-3">
                                    <button @click="tradeModalOpen = false" class="flex-1 px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 hover:text-white transition-colors text-sm font-medium">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js & GSAP Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // GSAP Animations
            const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

            // 1. Header
            tl.from('.gsap-header', {
                y: -30,
                opacity: 0,
                duration: 1
            })
            // 2. Stats Cards
            .from('.gsap-stat-card', {
                y: 30,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1
            }, '-=0.5')
            // 3. Charts
            .from('.gsap-chart', {
                scale: 0.95,
                opacity: 0,
                duration: 0.8,
                stagger: 0.2
            }, '-=0.5')
            // 4. Table Rows (Cascade)
            .to('.gsap-row', {
                opacity: 1,
                y: 0,
                duration: 0.5,
                stagger: 0.05
            }, '-=0.5');

            // 5. Number Counters
            gsap.utils.toArray('.count-up').forEach(el => {
                const finalValue = parseFloat(el.getAttribute('data-value'));
                const decimals = parseInt(el.getAttribute('data-decimals')) || 0;
                const prefix = el.getAttribute('data-prefix') || '';
                const suffix = el.getAttribute('data-suffix') || '';
                
                let proxy = { val: 0 };
                
                gsap.to(proxy, {
                    val: finalValue,
                    duration: 2.5,
                    ease: 'power4.out',
                    onUpdate: function() {
                        el.textContent = prefix + proxy.val.toFixed(decimals) + suffix;
                    }
                });
            });

            // 6. Shimmer Loop
            gsap.to('.shimmer-effect', {
                x: '200%',
                duration: 2,
                repeat: -1,
                repeatDelay: 3,
                ease: 'power1.inOut'
            });

            // 1. Equity Curve
            const equityCtx = document.getElementById('equityChart').getContext('2d');
            new Chart(equityCtx, {
                type: 'line',
                data: {
                    labels: @json($equityDates),
                    datasets: [{
                        label: 'Cumulative PnL',
                        data: @json($equityValues),
                        borderColor: '#10b981',
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                            return gradient;
                        },
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#9ca3af' } },
                        x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                    }
                }
            });

            // 2. Monthly Performance
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyPerformance->keys()),
                    datasets: [{
                        label: 'Monthly PnL',
                        data: @json($monthlyPerformance->values()),
                        backgroundColor: (context) => {
                            const value = context.raw;
                            return value >= 0 ? '#10b981' : '#ef4444';
                        },
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#9ca3af' } },
                        x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                    }
                }
            });

            // 3. Pair Distribution
            const pairCtx = document.getElementById('pairChart').getContext('2d');
            new Chart(pairCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($pairDistribution->keys()),
                    datasets: [{
                        data: @json($pairDistribution->values()),
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'right', labels: { color: '#9ca3af' } } 
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</x-dashboard-layout>
