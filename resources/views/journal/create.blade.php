<x-dashboard-layout title="Log Trade">
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data" 
                  x-data="tradeForm()" x-init="init()" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Trade Details -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Main Info Card -->
                        <div class="glass p-6 rounded-2xl border border-white/10">
                            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                                <div class="w-1 h-6 bg-primary rounded-full"></div>
                                Trade Details
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pair -->
                                <div>
                                    <x-input-label for="pair" value="Pair / Asset" />
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <x-text-input id="pair" name="pair" type="text" class="pl-10 block w-full uppercase" 
                                            placeholder="e.g. XAUUSD" required autofocus x-model="pair" @input="detectPipValue" />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('pair')" />
                                </div>

                                <!-- Type -->
                                <div>
                                    <x-input-label for="type" value="Position" />
                                    <div class="grid grid-cols-2 gap-4 mt-1">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="buy" class="peer sr-only" x-model="type">
                                            <div class="text-center py-2 rounded-lg border border-gray-700 bg-gray-900 text-gray-400 peer-checked:bg-green-500/20 peer-checked:text-green-500 peer-checked:border-green-500 transition-all">
                                                Buy (Long)
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="sell" class="peer sr-only" x-model="type">
                                            <div class="text-center py-2 rounded-lg border border-gray-700 bg-gray-900 text-gray-400 peer-checked:bg-red-500/20 peer-checked:text-red-500 peer-checked:border-red-500 transition-all">
                                                Sell (Short)
                                            </div>
                                        </label>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>

                                <!-- Entry Price -->
                                <div>
                                    <x-input-label for="entry_price" value="Entry Price" />
                                    <x-text-input id="entry_price" name="entry_price" type="number" step="0.00001" class="mt-1 block w-full" 
                                        required x-model="entry_price" @input="calculateStats" />
                                </div>

                                <!-- Exit Price -->
                                <div>
                                    <x-input-label for="exit_price" value="Exit Price" />
                                    <x-text-input id="exit_price" name="exit_price" type="number" step="0.00001" class="mt-1 block w-full" 
                                        x-model="exit_price" @input="calculateStats" />
                                </div>

                                <!-- Lot Size -->
                                <div>
                                    <x-input-label for="lot_size" value="Lot Size" />
                                    <x-text-input id="lot_size" name="lot_size" type="number" step="0.01" class="mt-1 block w-full" 
                                        required x-model="lot_size" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" value="Status" />
                                    <select id="status" name="status" class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="open">Open</option>
                                        <option value="closed">Closed</option>
                                        <option value="breakeven">Break Even</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Results Card -->
                        <div class="glass p-6 rounded-2xl border border-white/10">
                            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                                <div class="w-1 h-6 bg-secondary rounded-full"></div>
                                Results
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- PnL -->
                                <div>
                                    <x-input-label for="pnl" value="Profit/Loss ($)" />
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">$</span>
                                        </div>
                                        <x-text-input id="pnl" name="pnl" type="number" step="0.01" class="pl-8 block w-full font-bold" 
                                            x-bind:class="pnl > 0 ? 'text-green-500' : (pnl < 0 ? 'text-red-500' : 'text-white')"
                                            x-model="pnl" />
                                    </div>
                                </div>

                                <!-- Pips -->
                                <div>
                                    <x-input-label for="pips" value="Pips" />
                                    <div class="relative mt-1">
                                        <x-text-input id="pips" name="pips" type="number" step="0.1" class="block w-full font-bold" 
                                            x-bind:class="pips > 0 ? 'text-green-500' : (pips < 0 ? 'text-red-500' : 'text-white')"
                                            x-model="pips" readonly />
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-xs text-gray-500">Auto</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" x-text="pipMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Analysis & Meta -->
                    <div class="space-y-8">
                        <div class="glass p-6 rounded-2xl border border-white/10">
                            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                                <div class="w-1 h-6 bg-purple-500 rounded-full"></div>
                                Analysis
                            </h3>

                            <div class="space-y-6">
                                <!-- Dates -->
                                <div>
                                    <x-input-label for="open_date" value="Open Date" />
                                    <x-text-input id="open_date" name="open_date" type="datetime-local" class="mt-1 block w-full" :value="now()->format('Y-m-d\TH:i')" required />
                                </div>
                                <div>
                                    <x-input-label for="close_date" value="Close Date" />
                                    <x-text-input id="close_date" name="close_date" type="datetime-local" class="mt-1 block w-full" />
                                </div>

                                <!-- Emotion -->
                                <div>
                                    <x-input-label for="emotion" value="Emotion / Psychology" />
                                    <select id="emotion" name="emotion" class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="neutral">😐 Neutral (Calm)</option>
                                        <option value="confident">😎 Confident</option>
                                        <option value="fomo">😰 FOMO (Chasing)</option>
                                        <option value="fearful">😨 Fearful (Hesitant)</option>
                                        <option value="greedy">🤑 Greedy (Oversizing)</option>
                                        <option value="revenge">😡 Revenge Trading</option>
                                    </select>
                                </div>

                                <!-- Strategy -->
                                <div>
                                    <x-input-label for="strategy" value="Strategy Used" />
                                    <x-text-input id="strategy" name="strategy" type="text" class="mt-1 block w-full" placeholder="e.g. Support & Resistance" />
                                </div>

                                <!-- Notes -->
                                <div>
                                    <x-input-label for="notes" value="Notes / Analysis" />
                                    <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm"></textarea>
                                </div>

                                <!-- Screenshot -->
                                <div>
                                    <x-input-label for="screenshot" value="Chart Screenshot" />
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-700 border-dashed rounded-xl hover:border-primary transition-colors group cursor-pointer relative">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="text-sm text-gray-400">
                                                <label for="screenshot" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-primary-400 focus-within:outline-none">
                                                    <span>Upload a file</span>
                                                    <input id="screenshot" name="screenshot" type="file" class="sr-only">
                                                </label>
                                                <span class="pl-1">or drag and drop</span>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/10">
                    <a href="{{ route('journal.index') }}" class="text-gray-400 hover:text-white transition-colors">Cancel</a>
                    <x-primary-button class="px-8 py-3 text-lg">
                        {{ __('Save Trade') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function tradeForm() {
            return {
                pair: '',
                type: 'buy',
                entry_price: '',
                exit_price: '',
                lot_size: '',
                pnl: '',
                pips: '',
                pipValue: 0.0001, // Default to standard forex
                pipMessage: 'Standard Forex (0.0001)',

                init() {
                    this.$watch('pair', (value) => this.detectPipValue(value));
                    this.$watch('type', () => this.calculateStats());
                },

                detectPipValue(pair) {
                    const p = pair.toUpperCase();
                    
                    if (p.includes('JPY')) {
                        this.pipValue = 0.01;
                        this.pipMessage = 'JPY Pair (0.01)';
                    } else if (p === 'XAUUSD' || p === 'GOLD') {
                        this.pipValue = 0.10;
                        this.pipMessage = 'Gold (0.10)';
                    } else if (p === 'XAGUSD' || p === 'SILVER' || p.includes('OIL') || p === 'USOIL' || p === 'UKOIL' || p === 'WTI') {
                        this.pipValue = 0.01;
                        this.pipMessage = 'Silver/Oil (0.01)';
                    } else if (['BTCUSD', 'ETHUSD', 'US30', 'NAS100', 'SPX500', 'DAX40'].some(idx => p.includes(idx))) {
                        this.pipValue = 1;
                        this.pipMessage = 'Indices/Crypto (1.0)';
                    } else {
                        this.pipValue = 0.0001;
                        this.pipMessage = 'Standard Forex (0.0001)';
                    }
                    
                    this.calculateStats();
                },

                calculateStats() {
                    if (this.entry_price && this.exit_price) {
                        let diff = 0;
                        if (this.type === 'buy') {
                            diff = parseFloat(this.exit_price) - parseFloat(this.entry_price);
                        } else {
                            diff = parseFloat(this.entry_price) - parseFloat(this.exit_price);
                        }
                        
                        // Calculate Pips
                        this.pips = (diff / this.pipValue).toFixed(1);
                    }
                }
            }
        }
    </script>
</x-dashboard-layout>
