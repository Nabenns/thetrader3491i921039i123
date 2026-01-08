<x-dashboard-layout title="Trading Journal">
    <div class="py-12" x-data="{ goalModalOpen: false, tradeModalOpen: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Header & Goal -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h2 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-white mb-2">Trading Journal</h2>
                    <p class="text-gray-400">Track, Analyze, and Improve your trading performance.</p>
                </div>
                <div class="w-full md:w-1/3">
                    <div class="glass-card p-5 rounded-2xl">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-400 font-medium">Monthly Goal</span>
                            <span class="text-lg font-bold text-white">${{ number_format($goal->target_amount ?? 0, 0) }}</span>
                        </div>
                        <div class="w-full bg-black/20 rounded-full h-3 overflow-hidden backdrop-blur-sm border border-white/5">
                            @php
                                $progress = ($goal && $goal->target_amount > 0) ? min(($totalPnL / $goal->target_amount) * 100, 100) : 0;
                                $progressColor = $progress >= 100 ? 'bg-green-500' : 'bg-gradient-to-r from-primary to-secondary';
                            @endphp
                            <div class="{{ $progressColor }} h-full rounded-full transition-all duration-1000 ease-out relative" style="width: {{ max($progress, 0) }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-gray-500">Current: <span class="{{ $totalPnL >= 0 ? 'text-green-400' : 'text-red-400' }}">${{ number_format($totalPnL, 2) }}</span></span>
                            <button type="button" @click="goalModalOpen = true" class="text-xs font-medium text-primary hover:text-white hover:bg-primary/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer border border-transparent hover:border-primary/30">Edit Goal</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equity Curve Chart -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-white">Equity Curve</h3>
                    <span class="text-xs text-gray-400">Cumulative PnL Growth</span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="equityChart"></canvas>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-journal.stats-card 
                    title="Net PnL" 
                    value="${{ number_format($totalPnL, 2) }}" 
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    color="{{ $totalPnL >= 0 ? 'green-500' : 'red-500' }}"
                />
                <x-journal.stats-card 
                    title="Win Rate" 
                    value="{{ number_format($winRate, 1) }}%" 
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    color="blue-500"
                />
                <x-journal.stats-card 
                    title="Profit Factor" 
                    value="{{ number_format($profitFactor, 2) }}" 
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
                    color="purple-500"
                />
                <x-journal.stats-card 
                    title="Total Trades" 
                    value="{{ $journals->count() }}" 
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
                    color="orange-500"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Calendar & Recent Trades -->
                <div class="lg:col-span-2 space-y-8">
                    <x-journal.calendar :journals="$journals" />
                    
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                            <h3 class="text-xl font-bold text-white">Recent Trades</h3>
                            
                            <!-- Filter Bar -->
                            <form action="{{ route('journal.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                                @if(request('date'))
                                    <input type="hidden" name="date" value="{{ request('date') }}">
                                    <div class="px-3 py-1.5 rounded-lg bg-primary/20 text-primary text-xs font-bold flex items-center gap-2">
                                        Date: {{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}
                                        <a href="{{ route('journal.index', request()->except('date')) }}" class="hover:text-white">&times;</a>
                                    </div>
                                @endif

                                <select name="pair" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Pairs</option>
                                    @foreach($pairs as $pair)
                                        <option value="{{ $pair }}" {{ request('pair') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                                    @endforeach
                                </select>

                                <select name="type" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Types</option>
                                    <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Buy</option>
                                    <option value="sell" {{ request('type') == 'sell' ? 'selected' : '' }}>Sell</option>
                                </select>

                                <select name="outcome" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Outcomes</option>
                                    <option value="win" {{ request('outcome') == 'win' ? 'selected' : '' }}>Win</option>
                                    <option value="loss" {{ request('outcome') == 'loss' ? 'selected' : '' }}>Loss</option>
                                    <option value="break_even" {{ request('outcome') == 'break_even' ? 'selected' : '' }}>Break Even</option>
                                </select>

                                @if(request()->anyFilled(['date', 'pair', 'type', 'outcome']))
                                    <a href="{{ route('journal.index') }}" class="text-xs text-red-400 hover:text-red-300 underline">Reset</a>
                                @endif

                                <a href="{{ route('journal.export', request()->all()) }}" class="text-xs text-gray-400 hover:text-white flex items-center gap-1 ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    CSV
                                </a>

                                <a href="{{ route('journal.create') }}" class="btn-primary flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-shadow ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add
                                </a>
                            </form>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($journals->take(6) as $journal)
                                <x-journal.trade-card :journal="$journal" />
                            @empty
                                <div class="col-span-2 text-center py-12 text-gray-500 glass rounded-xl border border-dashed border-gray-700">
                                    <p class="mb-2">No trades recorded yet.</p>
                                    <a href="{{ route('journal.create') }}" class="text-primary hover:underline">Start your journey!</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Analytics (Placeholder for now) & Motivation -->
                <div class="space-y-8">
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-6">Top Pairs</h3>
                        
                        @php
                            $topPairs = $journals->groupBy('pair')->map->count()->sortDesc()->take(5);
                            $maxTrades = $topPairs->first() ?? 1;
                        @endphp

                        <div class="space-y-5">
                            @foreach($topPairs as $pair => $count)
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-300 font-medium">{{ $pair }}</span>
                                        <span class="text-gray-400 text-xs">{{ $count }} trades</span>
                                    </div>
                                    <div class="w-full bg-black/20 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-primary to-secondary h-full rounded-full transition-all duration-1000" style="width: {{ ($count / $maxTrades) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($topPairs->isEmpty())
                                <p class="text-center text-gray-500 text-sm py-4">No data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goal Modal -->
        <div 
            x-show="goalModalOpen" 
            style="display: none;"
            class="fixed inset-0 z-[60] overflow-y-auto" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div 
                    x-show="goalModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" 
                    aria-hidden="true" 
                    @click="goalModalOpen = false"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div 
                    x-show="goalModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[70]"
                >
                    <form action="{{ route('journal.goal') }}" method="POST" class="p-6">
                        @csrf
                        <h3 class="text-lg font-medium text-white mb-4">Set Monthly Goal</h3>
                        
                        <input type="hidden" name="month" value="{{ now()->month }}">
                        <input type="hidden" name="year" value="{{ now()->year }}">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Target Profit ($)</label>
                            <input type="number" name="target_amount" value="{{ $goal->target_amount ?? '' }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary" required>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="goalModalOpen = false" class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg">Save Goal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Trade Detail Modal -->
        <div 
            x-data="{ trade: null, loading: false }"
            @open-trade-modal.window="
                trade = null; 
                loading = true; 
                fetch('/journal/' + $event.detail.id).then(res => res.json()).then(data => { trade = data; loading = false; });
            "
            x-show="tradeModalOpen" 
            style="display: none;"
            class="fixed inset-0 z-[60] overflow-y-auto" 
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
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" 
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
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-[70]"
                >
                    <div class="p-6" x-show="loading">
                        <div class="flex justify-center items-center py-12">
                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <div x-show="!loading && trade">
                        <div class="relative h-48 bg-gray-800">
                            <template x-if="trade?.screenshot_url">
                                <img :src="trade.screenshot_url" class="w-full h-full object-cover opacity-50">
                            </template>
                            <template x-if="!trade?.screenshot_url">
                                <div class="w-full h-full flex items-center justify-center text-gray-600">No Screenshot</div>
                            </template>
                            <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-gray-900 to-transparent">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white" x-text="trade?.pair"></h2>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase" 
                                                :class="trade?.type === 'buy' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'"
                                                x-text="trade?.type"></span>
                                            <span class="text-gray-400 text-sm" x-text="trade?.open_date"></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold" 
                                            :class="trade?.pnl >= 0 ? 'text-green-400' : 'text-red-400'"
                                            x-text="(trade?.pnl >= 0 ? '+' : '') + '$' + Number(trade?.pnl).toFixed(2)"></div>
                                        <div class="text-gray-400 text-sm" x-text="trade?.pips + ' pips'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Entry Price</label>
                                    <div class="text-white font-mono" x-text="trade?.entry_price"></div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Exit Price</label>
                                    <div class="text-white font-mono" x-text="trade?.exit_price || '-'"></div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Lot Size</label>
                                    <div class="text-white font-mono" x-text="trade?.lot_size"></div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Strategy</label>
                                    <div class="text-white" x-text="trade?.strategy || '-'"></div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Emotion</label>
                                    <div class="text-white capitalize" x-text="trade?.emotion"></div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Notes</label>
                                    <div class="text-gray-300 text-sm" x-text="trade?.notes || 'No notes'"></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-white/10 flex justify-end gap-3">
                            <button @click="tradeModalOpen = false" class="px-4 py-2 text-gray-400 hover:text-white">Close</button>
                            <a :href="'/journal/' + trade?.id + '/edit'" class="btn-primary px-4 py-2 rounded-lg">Edit Trade</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('equityChart').getContext('2d');
            
            // Gradient for the area under the line
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(45, 212, 191, 0.2)'); // Primary color (Teal-400) with opacity
            gradient.addColorStop(1, 'rgba(45, 212, 191, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($equityDates),
                    datasets: [{
                        label: 'Equity Growth ($)',
                        data: @json($equityValues),
                        borderColor: '#2dd4bf', // Tailwind teal-400
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#131722', // Dark background
                        pointBorderColor: '#2dd4bf',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Hide points by default
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#2dd4bf',
                        pointHoverBorderColor: '#fff',
                        fill: true,
                        tension: 0.4 // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(19, 23, 34, 0.9)',
                            titleColor: '#9ca3af',
                            bodyColor: '#fff',
                            borderColor: 'rgba(45, 212, 191, 0.3)',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.03)',
                                borderDash: [5, 5],
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });

        function shareTrade(id) {
            // For now, just alert. In a real implementation, we would fetch trade data via AJAX
            // and show a beautiful modal that users can screenshot.
            // Or use html2canvas to generate an image.
            alert('Share feature coming soon! This will generate a beautiful image of your trade.');
        }
    </script>
</x-dashboard-layout>
