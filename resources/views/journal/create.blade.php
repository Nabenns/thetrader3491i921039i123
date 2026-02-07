<x-dashboard-layout title="Log Trade">
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data" x-data="tradeForm()"
                x-init="init()" @submit.prevent="submitForm" class="space-y-8">
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
                                <!-- Account -->
                                <div class="md:col-span-2">
                                    <x-input-label for="account_id" value="Trading Account" />
                                    <select id="account_id" name="account_id"
                                        class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="">Select Account (Optional)</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ (old('account_id') == $account->id || (isset($lastTrade) && $lastTrade->account_id == $account->id)) ? 'selected' : '' }}>
                                                {{ $account->name }} ({{ $account->currency }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                                </div>

                                <!-- Pair -->
                                <div>
                                    <x-input-label for="pair" value="Pair / Asset" />
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <x-text-input id="pair" name="pair" type="text"
                                            class="pl-10 block w-full uppercase" placeholder="e.g. XAUUSD" required
                                            autofocus x-model="pair" @input="detectPipValue" />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('pair')" />
                                </div>

                                <!-- Type -->
                                <div>
                                    <x-input-label for="type" value="Position" />
                                    <div class="grid grid-cols-2 gap-4 mt-1">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="buy" class="peer sr-only"
                                                x-model="type">
                                            <div
                                                class="text-center py-2 rounded-lg border border-gray-700 bg-gray-900 text-gray-400 peer-checked:bg-green-500/20 peer-checked:text-green-500 peer-checked:border-green-500 transition-all">
                                                Buy (Long)
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="sell" class="peer sr-only"
                                                x-model="type">
                                            <div
                                                class="text-center py-2 rounded-lg border border-gray-700 bg-gray-900 text-gray-400 peer-checked:bg-red-500/20 peer-checked:text-red-500 peer-checked:border-red-500 transition-all">
                                                Sell (Short)
                                            </div>
                                        </label>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>

                                <!-- Entry Price -->
                                <div>
                                    <x-input-label for="entry_price" value="Entry Price" />
                                    <x-text-input id="entry_price" name="entry_price" type="number" step="0.00001"
                                        class="mt-1 block w-full" required x-model="entry_price"
                                        @input="calculateStats" />
                                </div>

                                <!-- Exit Price -->
                                <div>
                                    <x-input-label for="exit_price" value="Exit Price" />
                                    <x-text-input id="exit_price" name="exit_price" type="number" step="0.00001"
                                        class="mt-1 block w-full" x-model="exit_price" @input="calculateStats" />
                                </div>

                                <!-- Lot Size -->
                                <div>
                                    <x-input-label for="lot_size" value="Lot Size" />
                                    <x-text-input id="lot_size" name="lot_size" type="number" step="0.01"
                                        class="mt-1 block w-full" required x-model="lot_size" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" value="Status" />
                                    <select id="status" name="status"
                                        class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
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
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">$</span>
                                        </div>
                                        <x-text-input id="pnl" name="pnl" type="number" step="0.01"
                                            class="pl-8 block w-full font-bold"
                                            x-bind:class="pnl > 0 ? 'text-green-500' : (pnl < 0 ? 'text-red-500' : 'text-white')"
                                            x-model="pnl" placeholder="0.00" />
                                    </div>
                                </div>

                                <!-- Pips -->
                                <div>
                                    <x-input-label for="pips" value="Pips" />
                                    <div class="relative mt-1">
                                        <x-text-input id="pips" name="pips" type="number" step="0.1"
                                            class="block w-full font-bold"
                                            x-bind:class="pips > 0 ? 'text-green-500' : (pips < 0 ? 'text-red-500' : 'text-white')"
                                            x-model="pips" readonly />
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
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
                                    <x-text-input id="open_date" name="open_date" type="datetime-local"
                                        class="mt-1 block w-full" :value="now()->format('Y-m-d\TH:i')" required />
                                </div>
                                <x-text-input id="close_date" name="close_date" type="datetime-local"
                                    class="mt-1 block w-full" />
                            </div>

                            <!-- Money Management -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="commission" value="Commission ($)" />
                                    <x-text-input id="commission" name="commission" type="number" step="0.01"
                                        class="mt-1 block w-full text-red-400" placeholder="-0.00"
                                        x-model="commission" />
                                </div>
                                <div>
                                    <x-input-label for="swap" value="Swap ($)" />
                                    <x-text-input id="swap" name="swap" type="number" step="0.01"
                                        class="mt-1 block w-full text-red-400" placeholder="-0.00" x-model="swap" />
                                </div>
                            </div>

                            <!-- Emotion -->
                            <div>
                                <x-input-label for="emotion" value="Emotion / Psychology" />
                                <input type="hidden" name="emotion" x-model="emotion">
                                <div class="grid grid-cols-3 gap-3 mt-1">
                                    <button type="button" @click="emotion = 'neutral'"
                                        :class="emotion === 'neutral' ? 'bg-primary/20 border-primary text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Neutral%20Face.png"
                                            alt="Neutral"
                                            class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">Neutral</span>
                                    </button>
                                    <button type="button" @click="emotion = 'confident'"
                                        :class="emotion === 'confident' ? 'bg-green-500/20 border-green-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Smiling%20Face%20with%20Sunglasses.png"
                                            alt="Confident"
                                            class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">Confident</span>
                                    </button>
                                    <button type="button" @click="emotion = 'fomo'"
                                        :class="emotion === 'fomo' ? 'bg-yellow-500/20 border-yellow-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Anguished%20Face.png"
                                            alt="FOMO" class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">FOMO</span>
                                    </button>
                                    <button type="button" @click="emotion = 'fearful'"
                                        :class="emotion === 'fearful' ? 'bg-orange-500/20 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Fearful%20Face.png"
                                            alt="Fearful"
                                            class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">Fearful</span>
                                    </button>
                                    <button type="button" @click="emotion = 'greedy'"
                                        :class="emotion === 'greedy' ? 'bg-red-500/20 border-red-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Money-Mouth%20Face.png"
                                            alt="Greedy"
                                            class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">Greedy</span>
                                    </button>
                                    <button type="button" @click="emotion = 'revenge'"
                                        :class="emotion === 'revenge' ? 'bg-red-700/20 border-red-700 text-white' : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-gray-600'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border transition-all group">
                                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Enraged%20Face.png"
                                            alt="Revenge"
                                            class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                                        <span class="text-xs font-medium">Revenge</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Strategy -->
                            <div>
                                <x-input-label for="strategy_id" value="Strategy Used" />
                                <div class="flex gap-2">
                                    <select id="strategy_id" name="strategy_id"
                                        class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="">Select Strategy</option>
                                        @foreach($strategies as $strategy)
                                            <option value="{{ $strategy->id }}" {{ old('strategy_id') == $strategy->id ? 'selected' : '' }}>
                                                {{ $strategy->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('strategies.index') }}" target="_blank"
                                        class="mt-1 p-2 bg-gray-800 border border-gray-700 rounded-lg text-gray-400 hover:text-white hover:border-gray-500 transition-colors flex items-center justify-center"
                                        title="Manage Strategies">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </a>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('strategy_id')" />
                            </div>

                            <!-- Tags -->
                            <div>
                                <x-input-label for="tags" value="Tags" />
                                <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full"
                                    placeholder="e.g. Trend, Breakout" x-model="tags" />
                                <div class="flex gap-2 mt-2 flex-wrap">
                                    <template
                                        x-for="tag in ['Trend', 'Breakout', 'Reversal', 'Scalp', 'Swing', 'News']">
                                        <button type="button" @click="addTag(tag)"
                                            class="px-2 py-1 bg-gray-800 rounded text-xs text-gray-400 hover:text-white hover:bg-gray-700 transition"
                                            x-text="tag"></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Notes (Simple Rich Text) -->
                            <div>
                                <x-input-label for="notes" value="Notes / Analysis" />
                                <div
                                    class="mt-1 rounded-xl overflow-hidden border border-gray-700 bg-gray-900 shadow-sm focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                                    <!-- Toolbar -->
                                    <div class="flex items-center gap-1 p-2 bg-black/20 border-b border-gray-700">
                                        <button type="button" @click="wrapText('**', '**')"
                                            class="p-1 px-2 text-gray-400 hover:text-white hover:bg-white/5 rounded text-xs font-bold"
                                            title="Bold">B</button>
                                        <button type="button" @click="wrapText('*', '*')"
                                            class="p-1 px-2 text-gray-400 hover:text-white hover:bg-white/5 rounded text-xs italic"
                                            title="Italic">I</button>
                                        <div class="w-px h-4 bg-gray-700 mx-1"></div>
                                        <button type="button" @click="insertText('- ')"
                                            class="p-1 px-2 text-gray-400 hover:text-white hover:bg-white/5 rounded text-xs"
                                            title="List">List</button>
                                        <button type="button" @click="insertText('# ')"
                                            class="p-1 px-2 text-gray-400 hover:text-white hover:bg-white/5 rounded text-xs font-bold"
                                            title="Header">H1</button>
                                    </div>
                                    <textarea id="notes" name="notes" rows="6"
                                        class="block w-full border-0 bg-transparent text-gray-300 focus:ring-0 p-3 resize-y placeholder-gray-600"
                                        placeholder="Write your thoughts... (Markdown supported)"
                                        x-ref="notesArea"></textarea>
                                </div>
                            </div>

                            <x-input-label for="images" value="Chart Screenshots (Max 5)" />
                            <div class="mt-1">
                                <!-- Preview Grid -->
                                <div x-show="imagePreviews.length > 0" class="grid grid-cols-2 gap-4 mb-4">
                                    <template x-for="(img, index) in imagePreviews" :key="index">
                                        <div
                                            class="relative rounded-xl overflow-hidden border border-gray-700 group aspect-video">
                                            <img :src="img" class="w-full h-full object-cover">
                                            <button type="button" @click="removeImage(index)"
                                                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Upload Box -->
                                <div x-show="imagePreviews.length < 5"
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-700 border-dashed rounded-xl hover:border-primary transition-colors group cursor-pointer relative"
                                    @dragover.prevent="$el.classList.add('border-primary')"
                                    @dragleave.prevent="$el.classList.remove('border-primary')"
                                    @drop.prevent="$el.classList.remove('border-primary'); handleDrop($event)">

                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary transition-colors"
                                            stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-400">
                                            <label for="images"
                                                class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-primary-400 focus-within:outline-none">
                                                <span>Upload files</span>
                                                <input id="images" name="images[]" type="file" class="sr-only"
                                                    accept="image/*" multiple @change="handleFileUpload">
                                            </label>
                                            <span class="pl-1">or drag and drop</span>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB each</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/10">
        <a href="{{ route('journal.index') }}" class="text-gray-400 hover:text-white transition-colors">Cancel</a>
        <x-primary-button id="submit-btn" class="px-8 py-3 text-lg">
            {{ __('Save Trade') }}
        </x-primary-button>
    </div>
    </form>
    </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
    <script>
        function tradeForm() {
            return {
                pips: '',
                tags: '',
                emotion: 'neutral',
                pair: '',
                type: 'buy',
                entry_price: '',
                exit_price: '',
                commission: '',
                swap: '',
                imagePreviews: [],
                filesToUpload: [], // Store actual File objects
                isSubmitting: false,
                pipValue: 0.0001,
                pipMessage: 'Standard Forex (0.0001)',

                init() {
                    this.$watch('pair', (value) => this.detectPipValue(value));
                    this.$watch('type', () => this.calculateStats());
                },

                handleFileUpload(event) {
                    const files = Array.from(event.target.files);
                    this.addFiles(files);
                },

                handleDrop(event) {
                    const files = Array.from(event.dataTransfer.files);
                    this.addFiles(files);
                },

                addFiles(files) {
                    // Update preview and store files
                    files.forEach(file => {
                        this.filesToUpload.push(file);
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreviews.push(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    });
                },

                removeImage(index) {
                    this.imagePreviews.splice(index, 1);
                    this.filesToUpload.splice(index, 1);
                },

                async submitForm(e) {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    const submitBtn = document.getElementById('submit-btn');
                    const originalText = submitBtn.innerText;
                    submitBtn.innerText = 'Compressing Images...';

                    try {
                        // Compress images
                        const dt = new DataTransfer();
                        const options = {
                            maxSizeMB: 1,
                            maxWidthOrHeight: 1920,
                            useWebWorker: true
                        };

                        for (const file of this.filesToUpload) {
                            if (file.type.startsWith('image/')) {
                                try {
                                    const compressedFile = await imageCompression(file, options);
                                    // Create a new File object with original name
                                    const newFile = new File([compressedFile], file.name, { type: file.type });
                                    dt.items.add(newFile);
                                } catch (err) {
                                    console.error('Compression failed for ' + file.name, err);
                                    dt.items.add(file); // Fallback to original
                                }
                            } else {
                                dt.items.add(file);
                            }
                        }

                        // Update input files
                        const input = document.getElementById('images');
                        input.files = dt.files;

                        submitBtn.innerText = 'Saving...';
                        this.$el.submit(); // Submit the form natively
                    } catch (error) {
                        console.error('Submission error', error);
                        this.isSubmitting = false;
                        submitBtn.innerText = originalText;
                        alert('Error preparing files. Please try again.');
                    }
                },

                // Rich Text Helpers (Unchanged)
                wrapText(start, end) {
                    const el = this.$refs.notesArea;
                    const val = el.value;
                    const selStart = el.selectionStart;
                    const selEnd = el.selectionEnd;
                    el.value = val.substring(0, selStart) + start + val.substring(selStart, selEnd) + end + val.substring(selEnd);
                    el.focus();
                    el.selectionStart = selEnd + start.length;
                    el.selectionEnd = selEnd + start.length;
                },

                insertText(text) {
                    const el = this.$refs.notesArea;
                    const val = el.value;
                    const selStart = el.selectionStart;
                    el.value = val.substring(0, selStart) + text + val.substring(selStart);
                    el.focus();
                    el.selectionStart = selStart + text.length;
                    el.selectionEnd = selStart + text.length;
                },

                addTag(tag) {
                    if (!this.tags) {
                        this.tags = tag;
                    } else {
                        const currentTags = this.tags.split(',').map(t => t.trim());
                        if (!currentTags.includes(tag)) {
                            this.tags += (this.tags.length > 0 ? ', ' : '') + tag;
                        }
                    }
                },

                detectPipValue(pair) {
                    // ... (Unchanged logic)
                    const p = pair.toUpperCase();
                    if (p.includes('JPY')) { this.pipValue = 0.01; this.pipMessage = 'JPY Pair (0.01)'; }
                    else if (p === 'XAUUSD' || p === 'GOLD') { this.pipValue = 0.10; this.pipMessage = 'Gold (0.10)'; }
                    else if (['XAGUSD', 'SILVER', 'OIL', 'USOIL', 'UKOIL', 'WTI'].some(x => p.includes(x))) { this.pipValue = 0.01; this.pipMessage = 'Silver/Oil (0.01)'; }
                    else if (['BTCUSD', 'ETHUSD', 'US30', 'NAS100', 'SPX500', 'DAX40'].some(idx => p.includes(idx))) { this.pipValue = 1; this.pipMessage = 'Indices/Crypto (1.0)'; }
                    else { this.pipValue = 0.0001; this.pipMessage = 'Standard Forex (0.0001)'; }
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
                        this.pips = (diff / this.pipValue).toFixed(1);
                    }
                }
            }
        }
    </script>
</x-dashboard-layout>