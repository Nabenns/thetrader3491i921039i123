<x-dashboard-layout title="Trading Journal">
    <div class="py-12" x-data="{ 
        accountModalOpen: false,
        goalModalOpen: false, 
        tradeModalOpen: false, 
        shareModalOpen: false, 
        importModalOpen: false, 
        tradeToShare: null,
        currency: 'USD',
        viewMode: localStorage.getItem('journalViewMode') || 'grid',
        rate: {{ $currencyRate }},
        toggleView(mode) {
            this.viewMode = mode;
            localStorage.setItem('journalViewMode', mode);
        },
        formatMoney(amount) {
            let val = this.currency === 'USD' ? amount : amount * this.rate;
            return new Intl.NumberFormat('en-US', { 
                style: 'currency', 
                currency: this.currency,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2 
            }).format(val);
        }
    }">
        <div id="journal-container" class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Header & Goal -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h2
                        class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-white mb-2">
                        Trading Journal</h2>
                    <p class="text-gray-400">Track, Analyze, and Improve your trading performance.</p>
                </div>
                <div class="w-full md:w-1/3">
                    <div class="glass-card p-5 rounded-2xl">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-400 font-medium">Monthly Goal</span>
                            <span class="text-lg font-bold text-white"
                                x-text="formatMoney({{ $goal->target_amount ?? 0 }})"></span>
                        </div>
                        <div
                            class="w-full bg-black/20 rounded-full h-3 overflow-hidden backdrop-blur-sm border border-white/5">
                            @php
                                $progress = ($goal && $goal->target_amount > 0) ? min(($totalPnL / $goal->target_amount) * 100, 100) : 0;
                                $progressColor = $progress >= 100 ? 'bg-green-500' : 'bg-gradient-to-r from-primary to-secondary';
                            @endphp
                            <div class="{{ $progressColor }} h-full rounded-full transition-all duration-1000 ease-out relative"
                                style="width: {{ max($progress, 0) }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-gray-500">Current: <span
                                    class="{{ $totalPnL >= 0 ? 'text-green-400' : 'text-red-400' }}"
                                    x-text="formatMoney({{ $totalPnL }})"></span></span>
                            <button type="button" @click="goalModalOpen = true"
                                class="text-xs font-medium text-primary hover:text-white hover:bg-primary/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer border border-transparent hover:border-primary/30">Edit
                                Goal</button>
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

            <!-- Heatmap -->
            <x-journal.heatmap :data="$heatmapData" />

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-journal.stats-card title="Net PnL" value="" x-text="formatMoney({{ $totalPnL }})"
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    color="{{ $totalPnL >= 0 ? 'green-500' : 'red-500' }}" />
                <x-journal.stats-card title="Win Rate" value="{{ number_format($winRate, 1) }}%"
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    color="blue-500" />
                <x-journal.stats-card title="Profit Factor" value="{{ number_format($profitFactor, 2) }}"
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
                    color="purple-500" />
                <x-journal.stats-card title="Total Trades" value="{{ $journals->count() }}"
                    icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
                    color="orange-500" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Calendar & Recent Trades -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                            <h3 class="text-xl font-bold text-white">Recent Trades</h3>

                            <!-- View Toggles -->
                            <div class="flex bg-black/40 rounded-lg p-1 border border-white/10">
                                <button @click="toggleView('grid')"
                                    :class="viewMode === 'grid' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                                    class="p-1.5 rounded-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                        </path>
                                    </svg>
                                </button>
                                <button @click="toggleView('list')"
                                    :class="viewMode === 'list' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                                    class="p-1.5 rounded-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>
                                <button @click="toggleView('calendar')" 
                                    :class="viewMode === 'calendar' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                                    class="p-1.5 rounded-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Filter Bar -->
                            <form id="journal-filter-form" action="{{ route('journal.index') }}" method="GET"
                                class="flex flex-wrap items-center gap-2">
                                <!-- Currency Toggle -->
                                <div class="mr-2">
                                    <button type="button" @click="currency = currency === 'USD' ? 'IDR' : 'USD'"
                                        class="px-3 py-1.5 rounded-lg bg-gray-800 border border-gray-700 text-xs font-bold text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                                        <span x-text="currency"></span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Account Selector -->
                                <select name="account_id" onchange="updateJournalParams()"
                                    class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Accounts</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="accountModalOpen = true" class="p-1.5 bg-gray-800 border border-gray-700 rounded-lg text-gray-400 hover:text-white hover:border-gray-500 transition-colors" title="Add Account">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </button>

                                <!-- Date Range -->
                                <div class="flex items-center gap-1 bg-gray-800 border border-gray-700 rounded-lg p-1">
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="updateJournalParams()" class="bg-transparent border-0 text-white text-xs p-1 focus:ring-0 w-24 placeholder-gray-500">
                                    <span class="text-gray-500 text-xs">-</span>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="updateJournalParams()" class="bg-transparent border-0 text-white text-xs p-1 focus:ring-0 w-24">
                                </div>

                                <select name="pair" onchange="updateJournalParams()"
                                    class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Pairs</option>
                                    @foreach($pairs as $pair)
                                        <option value="{{ $pair }}" {{ request('pair') == $pair ? 'selected' : '' }}>
                                            {{ $pair }}</option>
                                    @endforeach
                                </select>

                                <select name="type" onchange="updateJournalParams()"
                                    class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Types</option>
                                    <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Buy</option>
                                    <option value="sell" {{ request('type') == 'sell' ? 'selected' : '' }}>Sell</option>
                                </select>

                                <select name="outcome" onchange="updateJournalParams()"
                                    class="bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg focus:ring-primary focus:border-primary block p-1.5">
                                    <option value="">All Outcomes</option>
                                    <option value="win" {{ request('outcome') == 'win' ? 'selected' : '' }}>Win</option>
                                    <option value="loss" {{ request('outcome') == 'loss' ? 'selected' : '' }}>Loss
                                    </option>
                                    <option value="break_even" {{ request('outcome') == 'break_even' ? 'selected' : '' }}>
                                        Break Even</option>
                                </select>

                                @if(request()->anyFilled(['date_from', 'date_to', 'pair', 'type', 'outcome', 'account_id']))
                                    <a href="{{ route('journal.index') }}" class="text-xs text-red-500 hover:text-red-400 font-bold px-2">Reset</a>
                                @endif

                                <div class="h-6 w-px bg-gray-700 mx-2"></div>

                                <button type="button" @click="importModalOpen = true"
                                    class="text-xs text-gray-400 hover:text-white flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Import
                                </button>

                                <a href="{{ route('journal.export', request()->all()) }}"
                                    class="text-xs text-gray-400 hover:text-white flex items-center gap-1 ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Export
                                </a>

                                <a href="{{ route('journal.create') }}"
                                    class="btn-primary flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-shadow ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Trade
                                </a>
                            </form>
                        </div>
                            <!-- Grid View -->
                        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($journals as $journal)
                                <x-journal.trade-card :journal="$journal" />
                            @empty
                                <div
                                    class="col-span-2 text-center py-16 text-gray-500 glass rounded-xl border border-dashed border-gray-700 flex flex-col items-center justify-center group hover:border-primary/50 transition-colors">
                                    <div
                                        class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-8 h-8 text-gray-600 group-hover:text-primary transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-1">Start Your Journey</h3>
                                    <p class="mb-4 text-sm max-w-xs mx-auto">No trades recorded properly yet. Consistency
                                        starts with the first entry.</p>
                                    <a href="{{ route('journal.create') }}"
                                        class="btn-primary px-6 py-2 rounded-lg shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all">Log
                                        First Trade</a>
                                </div>
                            @endforelse
                        </div>

                        <!-- Calendar View -->
                        <div x-show="viewMode === 'calendar'" style="display: none;">
                            <x-journal.calendar :journals="$journals" />
                        </div>

                        <!-- List View -->
                        <div x-show="viewMode === 'list'" style="display: none;">
                            <div class="overflow-x-auto rounded-xl border border-white/10 glass">
                                <table class="w-full text-sm text-left text-gray-300">
                                    <thead class="text-xs uppercase bg-black/20 text-gray-400 border-b border-white/5">
                                        <tr>
                                            <th class="px-6 py-4 rounded-tl-xl">Date</th>
                                            <th class="px-6 py-4">Pair</th>
                                            <th class="px-6 py-4">Type</th>
                                            <th class="px-6 py-4 text-right">PnL</th>
                                            <th class="px-6 py-4 text-center">Outcome</th>
                                            <th class="px-6 py-4">Strategy</th>
                                            <th class="px-6 py-4 text-right rounded-tr-xl">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @forelse($journals as $journal)
                                            <tr class="hover:bg-white/5 transition-colors group cursor-pointer"
                                                @click="window.dispatchEvent(new CustomEvent('open-trade-modal', { detail: { id: {{ $journal->id }} } }))">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="font-bold text-white">
                                                        {{ $journal->open_date->format('d M') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $journal->open_date->format('H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 font-bold tracking-wider">{{ $journal->pair }}</td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $journal->type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                                        {{ $journal->type }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-right font-mono font-bold {{ $journal->pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $journal->pnl >= 0 ? '+' : '' }}<span
                                                        x-text="formatMoney({{ $journal->pnl }})"></span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if($journal->pnl > 0)
                                                        <span
                                                            class="inline-block w-2 h-2 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                                                    @elseif($journal->pnl < 0)
                                                        <span
                                                            class="inline-block w-2 h-2 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                                                    @else
                                                        <span class="inline-block w-2 h-2 rounded-full bg-gray-500"></span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 truncate max-w-[150px]">
                                                    @if($journal->tradingStrategy)
                                                        <span class="px-2 py-1 rounded text-xs font-medium border" 
                                                            style="background-color: {{ $journal->tradingStrategy->color }}10; color: {{ $journal->tradingStrategy->color }}; border-color: {{ $journal->tradingStrategy->color }}20">
                                                            {{ $journal->tradingStrategy->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">{{ $journal->strategy ?? '-' }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-right relative z-20">
                                                    <!-- Flex/Share Button -->
                                                    <button @click.stop="$dispatch('open-share-modal', { id: {{ $journal->id }} })"
                                                        class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-lg hover:shadow-purple-500/20 transition-all hover:-translate-y-0.5"
                                                        title="Flex This Trade">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                                    No trades found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $journals->links() }}
                        </div>
                    </div>
                </div>

                <!-- Right Column: Analytics & Stats -->
                <div class="space-y-6">
                    <!-- Win Rate by Day -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-4">Win Rate by Day</h3>
                        <div class="space-y-3">
                            @foreach($winRateByDay as $day => $rate)
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-400">{{ $day }}</span>
                                        <span class="{{ $rate >= 50 ? 'text-green-400' : 'text-gray-300' }} font-bold">{{ number_format($rate, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden border border-white/5">
                                        <div class="{{ $rate >= 50 ? 'bg-green-500' : 'bg-gray-500' }} h-full rounded-full transition-all duration-1000" style="width: {{ $rate }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Win Rate by Session -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-4">Win Rate by Session</h3>
                        <div class="space-y-3">
                            @foreach($winRateBySession as $session => $rate)
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-400">{{ $session }}</span>
                                        <span class="{{ $rate >= 50 ? 'text-green-400' : 'text-gray-300' }} font-bold">{{ number_format($rate, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden border border-white/5">
                                        <div class="{{ $rate >= 50 ? 'bg-blue-500' : 'bg-gray-500' }} h-full rounded-full transition-all duration-1000" style="width: {{ $rate }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
                                        <div class="bg-gradient-to-r from-primary to-secondary h-full rounded-full transition-all duration-1000"
                                            style="width: {{ ($count / $maxTrades) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach

                            @if($topPairs->isEmpty())
                                <p class="text-center text-gray-500 text-sm py-4">No data available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Long vs Short -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-6">Long vs Short</h3>
                        <div class="space-y-6">
                            <!-- Long -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-medium text-green-400">LONG ({{ $longCount }})</span>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-white">{{ number_format($longWinRate, 1) }}% WR</div>
                                        <div class="text-xs {{ $longPnL >= 0 ? 'text-green-400' : 'text-red-400' }}"
                                             x-text="formatMoney({{ $longPnL }})">
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                                    <div class="bg-green-500 h-full rounded-full transition-all duration-1000"
                                        style="width: {{ $longWinRate }}%"></div>
                                </div>
                            </div>
                            <!-- Short -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-medium text-red-400">SHORT ({{ $shortCount }})</span>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-white">{{ number_format($shortWinRate, 1) }}% WR</div>
                                        <div class="text-xs {{ $shortPnL >= 0 ? 'text-green-400' : 'text-red-400' }}"
                                             x-text="formatMoney({{ $shortPnL }})">
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                                    <div class="bg-red-500 h-full rounded-full transition-all duration-1000"
                                        style="width: {{ $shortWinRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hourly Performance -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-6">Hourly Stats</h3>
                        <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($hourlyStats as $stat)
                                <div>
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="text-xs text-gray-400 font-mono">{{ sprintf('%02d:00', $stat['hour']) }}</span>
                                        <span class="text-xs font-bold {{ $stat['pnl'] >= 0 ? 'text-green-400' : 'text-red-400' }}"
                                              x-text="formatMoney({{ $stat['pnl'] }})">
                                        </span>
                                    </div>
                                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $stat['win_rate'] >= 50 ? 'bg-primary' : 'bg-gray-600' }} h-full rounded-full"
                                            style="width: {{ $stat['win_rate'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                            @if($hourlyStats->isEmpty())
                                <p class="text-center text-gray-500 text-sm">No data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goal Modal -->
        <div x-show="goalModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="goalModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="goalModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="goalModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[70]">
                    <form action="{{ route('journal.goal') }}" method="POST" class="p-6">
                        @csrf
                        <h3 class="text-lg font-medium text-white mb-4">Set Monthly Goal</h3>

                        <input type="hidden" name="month" value="{{ now()->month }}">
                        <input type="hidden" name="year" value="{{ now()->year }}">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Target Profit ($)</label>
                            <input type="number" name="target_amount" value="{{ $goal->target_amount ?? '' }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary"
                                required>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="goalModalOpen = false"
                                class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg">Save Goal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Trade Detail Modal -->
        <div x-data="{ trade: null, loading: false }" @open-trade-modal.window="
                trade = null; 
                loading = true; 
                fetch('/journal/' + $event.detail.id).then(res => res.json()).then(data => { trade = data; loading = false; });
            " x-show="tradeModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="tradeModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="tradeModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="tradeModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full z-[70]">
                    <div class="p-6" x-show="loading">
                        <div class="flex justify-center items-center py-12">
                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
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
                                    x-text="(currency === 'USD' && trade?.pnl >= 0 ? '+' : '') + formatMoney(trade?.pnl)">
                                </div>
                                <div class="text-sm font-medium text-gray-400" x-text="trade?.pips + ' pips'"></div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <!-- Left: Chart (Hero) -->
                            <div
                                class="w-full md:w-2/3 bg-gray-900/50 relative min-h-[300px] md:min-h-[400px] border-r border-white/5">
                                <template x-if="trade?.screenshot_url">
                                    <img :src="trade.screenshot_url"
                                        class="absolute inset-0 w-full h-full object-contain bg-black/40">
                                </template>
                                <template x-if="!trade?.screenshot_url">
                                    <div
                                        class="absolute inset-0 flex flex-col items-center justify-center text-gray-600">
                                        <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
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
                                        <div class="text-sm font-medium text-white truncate">
                                            <template x-if="trade?.trading_strategy">
                                                <span class="px-2 py-0.5 rounded text-xs border"
                                                    :style="'background-color: ' + trade.trading_strategy.color + '10; color: ' + trade.trading_strategy.color + '; border-color: ' + trade.trading_strategy.color + '20'"
                                                    x-text="trade.trading_strategy.name"></span>
                                            </template>
                                            <template x-if="!trade?.trading_strategy">
                                                <span x-text="trade?.strategy || '-'"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-black/20 border border-white/5">
                                        <label
                                            class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1 block">Emotion</label>
                                        <div class="flex items-center gap-2">
                                            <span class="capitalize text-sm font-medium text-white"
                                                x-text="trade?.emotion"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trade Stats -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Entry Price</span>
                                        <span class="text-sm font-mono font-bold text-white"
                                            x-text="formatMoney(trade?.entry_price)"></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Exit Price</span>
                                        <span class="text-sm font-mono font-bold text-white"
                                            x-text="trade?.exit_price ? formatMoney(trade?.exit_price) : '-'"></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                                        <span class="text-sm text-gray-400">Lot Size</span>
                                        <span class="text-sm font-mono font-bold text-white"
                                            x-text="trade?.lot_size"></span>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="flex-1">
                                    <label
                                        class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-2 block">Notes</label>
                                    <div class="p-4 rounded-xl bg-black/20 border border-white/5 min-h-[100px] max-h-[200px] overflow-y-auto text-sm text-gray-300 leading-relaxed"
                                        x-text="trade?.notes || 'No notes added.'"></div>
                                </div>

                                <!-- Actions -->
                                <div class="pt-4 mt-auto flex gap-3">
                                    <button @click="tradeModalOpen = false"
                                        class="flex-1 px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 hover:text-white transition-colors text-sm font-medium">Close</button>
                                    <a :href="'/journal/' + trade?.id + '/edit'"
                                        class="flex-1 px-4 py-2 rounded-lg bg-primary hover:bg-primary-600 text-white text-center transition-colors text-sm font-medium shadow-lg shadow-primary/20">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Import Modal -->
        <div x-show="importModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="importModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="importModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="importModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[70]">
                    <form action="{{ route('journal.import') }}" method="POST" enctype="multipart/form-data"
                        class="p-6">
                        @csrf
                        <h3 class="text-lg font-medium text-white mb-4">Import Trades</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Target Account</label>
                                <select name="account_id"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary"
                                    required>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">CSV File</label>
                                <input type="file" name="file" accept=".csv,.txt"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-600"
                                    required>
                                <p class="mt-1 text-xs text-gray-500">Upload CSV file matching the template.</p>
                            </div>

                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3 flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-200">Need the template?</p>
                                    <a href="{{ route('journal.template') }}"
                                        class="text-xs text-blue-400 hover:text-blue-300 underline">Download CSV
                                        Template</a>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="importModalOpen = false"
                                class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg">Import Trades</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Account Modal -->
        <div x-show="accountModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="accountModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="accountModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="accountModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[70]">
                    <form action="{{ route('account.store') }}" method="POST" class="p-6">
                        @csrf
                        <h3 class="text-lg font-medium text-white mb-4">Add Trading Account</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Account Name</label>
                                <input type="text" name="name" placeholder="e.g. OANDA Standard" class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Broker</label>
                                <input type="text" name="broker" placeholder="e.g. OANDA" class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Initial Balance</label>
                                    <input type="number" name="balance" step="0.01" placeholder="1000" class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Currency</label>
                                    <select name="currency" class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-primary focus:border-primary">
                                        <option value="USD">USD</option>
                                        <option value="IDR">IDR</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                        <option value="JPY">JPY</option>
                                        <option value="AUD">AUD</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="accountModalOpen = false" class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Share Modal (Flex Card) -->
                <div x-show="shareModalOpen" @open-share-modal.window="
                    tradeToShare = window.allTrades.find(t => t.id === $event.detail.id);
                    shareModalOpen = true;
                " x-data="{ selectedBg: 'bg-gradient-to-br from-[#111827] via-[#0f172a] to-black' }" style="display: none;" class="fixed inset-0 z-[80] overflow-y-auto" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="shareModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="shareModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="shareModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-middle glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full z-[90]">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white mb-4 text-center">Share Your Win</h3>

                        <!-- Background Selector -->
                        <div class="flex justify-center gap-3 mb-6">
                            <button @click="selectedBg = 'bg-gradient-to-br from-[#111827] via-[#0f172a] to-black'"
                                class="w-8 h-8 rounded-full border-2 border-white/20 hover:scale-110 transition-transform bg-gradient-to-br from-[#111827] via-[#0f172a] to-black"
                                :class="selectedBg === 'bg-gradient-to-br from-[#111827] via-[#0f172a] to-black' ? 'ring-2 ring-white ring-offset-2 ring-offset-black' : ''"
                                title="Midnight"></button>
                            <button @click="selectedBg = 'bg-gradient-to-br from-emerald-900 via-green-900 to-black'"
                                class="w-8 h-8 rounded-full border-2 border-white/20 hover:scale-110 transition-transform bg-gradient-to-br from-emerald-900 via-green-900 to-black"
                                :class="selectedBg === 'bg-gradient-to-br from-emerald-900 via-green-900 to-black' ? 'ring-2 ring-white ring-offset-2 ring-offset-black' : ''"
                                title="Emerald"></button>
                            <button @click="selectedBg = 'bg-gradient-to-br from-blue-900 via-indigo-900 to-black'"
                                class="w-8 h-8 rounded-full border-2 border-white/20 hover:scale-110 transition-transform bg-gradient-to-br from-blue-900 via-indigo-900 to-black"
                                :class="selectedBg === 'bg-gradient-to-br from-blue-900 via-indigo-900 to-black' ? 'ring-2 ring-white ring-offset-2 ring-offset-black' : ''"
                                title="Ocean"></button>
                            <button @click="selectedBg = 'bg-gradient-to-br from-purple-900 via-fuchsia-900 to-black'"
                                class="w-8 h-8 rounded-full border-2 border-white/20 hover:scale-110 transition-transform bg-gradient-to-br from-purple-900 via-fuchsia-900 to-black"
                                :class="selectedBg === 'bg-gradient-to-br from-purple-900 via-fuchsia-900 to-black' ? 'ring-2 ring-white ring-offset-2 ring-offset-black' : ''"
                                title="Cosmic"></button>
                            <button @click="selectedBg = 'bg-gradient-to-br from-orange-900 via-red-900 to-black'"
                                class="w-8 h-8 rounded-full border-2 border-white/20 hover:scale-110 transition-transform bg-gradient-to-br from-orange-900 via-red-900 to-black"
                                :class="selectedBg === 'bg-gradient-to-br from-orange-900 via-red-900 to-black' ? 'ring-2 ring-white ring-offset-2 ring-offset-black' : ''"
                                title="Sunset"></button>
                        </div>

                        <!-- Flex Card Container (This is what gets captured) -->
                        <div id="flex-card"
                            class="relative w-full aspect-square rounded-xl overflow-hidden border border-white/10 shadow-2xl flex flex-col justify-between p-8"
                            :class="selectedBg">
                            <!-- Background Effects -->
                            <div class="absolute top-0 right-0 w-64 h-64 rounded-full blur-3xl -mr-32 -mt-32"
                                style="background-color: rgba(128, 170, 179, 0.2);"></div>
                            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full blur-3xl -ml-32 -mb-32"
                                style="background-color: rgba(92, 133, 141, 0.2);"></div>

                            <!-- Header -->
                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo"
                                        class="w-full h-full object-contain" />
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-lg leading-none">TheTrader.id</h4>
                                    <span class="text-gray-400 text-xs">Official Trading Journal</span>
                                </div>
                            </div>

                            <!-- Main Content -->
                            <div class="text-center relative z-10 space-y-2">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full backdrop-blur-md mb-2"
                                    style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <span class="w-2 h-2 rounded-full"
                                        :style="tradeToShare?.type === 'buy' ? 'background-color: #22c55e' : 'background-color: #ef4444'"></span>
                                    <span class="text-sm font-bold uppercase tracking-wider text-white"
                                        x-text="tradeToShare?.type"></span>
                                </div>

                                <h1 class="text-5xl font-black text-white tracking-tight" x-text="tradeToShare?.pair">
                                </h1>

                                <div class="flex justify-center items-baseline gap-1 mt-4">
                                    <span class="text-4xl font-bold"
                                        :class="tradeToShare?.pnl >= 0 ? 'drop-shadow-[0_0_15px_rgba(74,222,128,0.5)]' : 'drop-shadow-[0_0_15px_rgba(248,113,113,0.5)]'"
                                        :style="tradeToShare?.pnl >= 0 ? 'color: #4ade80' : 'color: #f87171'"
                                        x-text="(currency === 'USD' && tradeToShare?.pnl >= 0 ? '+' : '') + formatMoney(tradeToShare?.pnl)">
                                    </span>
                                </div>
                                <p class="text-gray-400 text-sm" x-text="tradeToShare?.pips + ' pips captured'"></p>
                            </div>

                            <!-- Footer -->
                            <div
                                class="flex justify-between items-end relative z-10 border-t border-white/10 pt-4 mt-4">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Date</p>
                                    <p class="text-white text-sm font-medium"
                                        x-text="new Date(tradeToShare?.open_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })">
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center gap-1 text-xs font-bold px-2 py-1 rounded border"
                                        style="color: #4ade80; background-color: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.2);">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        VERIFIED TRADE
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="shareModalOpen = false"
                                class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button onclick="downloadFlexCard()"
                                class="btn-primary px-6 py-2 rounded-lg flex items-center gap-2 shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        // Global Trades Data
        window.allTrades = {{ Js::from($journals->items()) }};

        function openShareModal(id) {
            window.dispatchEvent(new CustomEvent('open-share-modal', { detail: { id: id } }));
        }

        function downloadFlexCard() {
            const element = document.getElementById('flex-card');
            if(!element) return;
            
            // Show loading or visual feedback if needed
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Generating...';

            html2canvas(element, {
                backgroundColor: null,
                scale: 3, // High resolution
                useCORS: true,
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'TheTrader-' + (window.tradeToShare?.pair || 'trade') + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = originalText;
            }).catch(err => {
                console.error(err);
                alert('Failed to generate image.');
                btn.innerHTML = originalText;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.initEquityChart();
        });

        // Initialize Data
        window.equityMeta = @json($equityMeta);

        // Refactored Chart Logic for re-initialization
        window.initEquityChart = function() {
            const canvas = document.getElementById('equityChart');
            if(!canvas) return;
            
            // Destroy existing chart if any
            if(window.equityChartInstance) {
                window.equityChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            // ... gradient ... (keep existing)
            // Re-define gradient because context changed
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(45, 212, 191, 0.2)');
            gradient.addColorStop(1, 'rgba(45, 212, 191, 0)');

            window.equityChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($equityDates),
                    datasets: [{
                        label: 'Equity Growth ($)',
                        data: @json($equityValues),
                        borderColor: '#2dd4bf', 
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#131722', 
                        pointBorderColor: '#2dd4bf',
                        pointBorderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#2dd4bf',
                        pointHoverBorderColor: '#fff',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
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
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    }
                                    
                                    // Add Metadata
                                    const index = context.dataIndex;
                                    if(window.equityMeta && window.equityMeta[index]) {
                                        const meta = window.equityMeta[index];
                                        if(meta.count > 0) {
                                            const pnlSign = meta.pnl >= 0 ? '+' : '';
                                            label += ` (${meta.count} Trades, ${pnlSign}$${meta.pnl})`;
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    // ... scales ...
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { color: '#6b7280', font: { size: 11 } } },
                        y: { grid: { color: 'rgba(255, 255, 255, 0.03)', borderDash: [5, 5], drawBorder: false }, ticks: { color: '#6b7280', font: { size: 11 }, callback: function(val){ return '$' + val; } } }
                    },
                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                }
            });
        };

        function getFilterForm() {
            return document.getElementById('journal-filter-form');
        }

        function updateJournalParams() {
            const form = getFilterForm();
            if(!form) return;
            
            const container = document.getElementById('journal-container');
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = form.action + '?' + params.toString();

            window.history.pushState({}, '', url);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('journal-container').innerHTML;
                
                container.innerHTML = newContent;
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
                
                // Re-init Chart
                if(window.initEquityChart) window.initEquityChart();
                
                // Re-init Alpine (Simple Reload if complex)
                // For now, let's assume the main x-data parent handles the new children bindings.
                // NOTE: Alpine 3 needs explicit re-scanning if replacing innerHTML of a component root.
                // Since 'journal-container' is INSIDE the 'x-data' root, Alpine MIGHT see the DOM change if observing.
                // But usually it doesn't. 
                // We'll leave it as is. If interactivity breaks, we might need a full reload fallback.
            })
            .catch(err => {
                console.error('Filter error:', err);
                window.location.reload(); // Fallback
            });
        }

        function selectDate(date) {
            const form = getFilterForm();
            if(!form) return;
            form.querySelector('input[name="date_from"]').value = date;
            form.querySelector('input[name="date_to"]').value = date;
            updateJournalParams();
        }

        function downloadFlexCard() {
            const element = document.getElementById('flex-card');
            const button = event.currentTarget;
            const originalText = button.innerHTML;

            // Loading state
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';

            html2canvas(element, {
                backgroundColor: null,
                scale: 2, // High resolution
                logging: false,
                useCORS: true // Important for images
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'thetrader-flex-' + Date.now() + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            }).catch(err => {
                console.error('Error generating image:', err);
                alert('Failed to generate image. Please try again.');
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }
    </script>
</x-dashboard-layout>