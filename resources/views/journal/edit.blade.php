<x-dashboard-layout title="Edit Trade">
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-white">Edit Trade</h2>
                    <p class="text-gray-400">Update your trade details.</p>
                </div>
                <form action="{{ route('journal.destroy', $journal) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this trade?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1 bg-red-500/10 px-4 py-2 rounded-lg hover:bg-red-500/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete Trade
                    </button>
                </form>
            </div>

            <form action="{{ route('journal.update', $journal) }}" method="POST" enctype="multipart/form-data"
                x-data="tradeForm()" x-init="init()" @submit.prevent="submitForm" class="space-y-8">
                @csrf
                @method('PUT')

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
                                            <option value="{{ $account->id }}" {{ (old('account_id', $journal->account_id) == $account->id) ? 'selected' : '' }}>
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
                                            x-model="pair" @input="detectPipValue" />
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
                                        class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm"
                                        x-model="status">
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
                                            x-model="pnl" />
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
                                        class="mt-1 block w-full" :value="old('open_date', $journal->open_date->format('Y-m-d\TH:i'))" required />
                                </div>
                                <x-text-input id="close_date" name="close_date" type="datetime-local"
                                    class="mt-1 block w-full" :value="old('close_date', $journal->close_date ? $journal->close_date->format('Y-m-d\TH:i') : '')" />
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
                                <select id="emotion" name="emotion"
                                    class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm"
                                    x-model="emotion">
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
                                <x-input-label for="strategy_id" value="Strategy Used" />
                                <div class="flex gap-2">
                                    <select id="strategy_id" name="strategy_id"
                                        class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="">Select Strategy</option>
                                        @foreach($strategies as $strategy)
                                            <option value="{{ $strategy->id }}" {{ (old('strategy_id', $journal->strategy_id) == $strategy->id) ? 'selected' : '' }}>
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
                                    :value="old('tags', implode(', ', $journal->tags ?? []))"
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
                                        placeholder="Write your thoughts..."
                                        x-ref="notesArea">{{ old('notes', $journal->notes) }}</textarea>
                                </div>
                            </div>

                            <x-input-label for="images" value="Chart Screenshots" />
                            <div class="mt-1">
                                <!-- Preview Grid -->
                                <div class="grid grid-cols-2 gap-4 mb-4"
                                    x-show="serverImages.length > 0 || imagePreviews.length > 0">
                                    <!-- Existing Images -->
                                    <template x-for="(img, index) in serverImages" :key="'server-' + img.id">
                                        <div
                                            class="relative rounded-xl overflow-hidden border border-gray-700 group aspect-video">
                                            <img :src="img.url" class="w-full h-full object-cover">
                                            <button type="button" @click="removeServerImage(img.id, index)"
                                                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <!-- New Uploads -->
                                    <template x-for="(img, index) in imagePreviews" :key="'new-' + index">
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
                                            <div
                                                class="absolute bottom-2 left-2 px-2 py-1 bg-black/50 text-white text-[10px] rounded">
                                                New</div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Upload Box -->
                                <div x-show="serverImages.length + imagePreviews.length < 5"
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

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/10">
            <a href="{{ route('journal.index') }}" class="text-gray-400 hover:text-white transition-colors">Cancel</a>
            <x-primary-button id="submit-btn" class="px-8 py-3 text-lg">
                {{ __('Update Trade') }}
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
                pair: '{{ old('pair', $journal->pair) }}',
                type: '{{ old('type', $journal->type) }}',
                entry_price: '{{ old('entry_price', $journal->entry_price) }}',
                exit_price: '{{ old('exit_price', $journal->exit_price) }}',
                lot_size: '{{ old('lot_size', $journal->lot_size) }}',
                pnl: '{{ old('pnl', $journal->pnl) }}',
                pips: '{{ old('pips', $journal->pips) }}',
                commission: '{{ old('commission', $journal->commission) }}',
                swap: '{{ old('swap', $journal->swap) }}',
                tags: '{{ old('tags', implode(', ', $journal->tags ?? [])) }}',
                status: '{{ old('status', $journal->status) }}',
                emotion: '{{ old('emotion', $journal->emotion) }}',
                pipValue: 0.0001,
                pipMessage: 'Standard Forex (0.0001)',

                serverImages: [
                    @foreach($journal->images as $img)
                        { id: {{ $img->id }}, url: '{{ Storage::url($img->image_path) }}' },
                    @endforeach
                ],
                imagePreviews: [], // For NEW uploads
                filesToUpload: [], // Store actual File objects
                isSubmitting: false,

                init() {
                    this.detectPipValue(this.pair);
                    this.$watch('pair', (value) => this.detectPipValue(value));
                    this.$watch('type', () => this.calculateStats());
                },

                // Image Handling
                handleFileUpload(event) {
                    const files = Array.from(event.target.files);
                    this.addFiles(files);
                },

                handleDrop(event) {
                    const files = Array.from(event.dataTransfer.files);
                    this.addFiles(files);
                },

                addFiles(files) {
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
                    submitBtn.innerText = 'Compressing & Uploading...';

                    try {
                        const dt = new DataTransfer();
                        const options = { maxSizeMB: 1, maxWidthOrHeight: 1920, useWebWorker: true };

                        for (const file of this.filesToUpload) {
                            if (file.type.startsWith('image/')) {
                                try {
                                    const compressedFile = await imageCompression(file, options);
                                    const newFile = new File([compressedFile], file.name, { type: file.type });
                                    dt.items.add(newFile);
                                } catch (err) {
                                    dt.items.add(file);
                                }
                            } else {
                                dt.items.add(file);
                            }
                        }

                        const input = document.getElementById('images');
                        input.files = dt.files;

                        submitBtn.innerText = 'Updating...';
                        this.$el.submit();
                    } catch (error) {
                        console.error('Submission error', error);
                        this.isSubmitting = false;
                        submitBtn.innerText = originalText;
                        alert('Error preparing files.');
                    }
                },

                removeServerImage(id, index) {
                    if (!confirm('Delete this image permanently?')) return;

                    fetch('/journal/image/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.serverImages.splice(index, 1);
                            } else {
                                alert('Failed to delete image.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error deleting image.');
                        });
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

                // Rich Text Helpers
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

                detectPipValue(pair) {
                    if (!pair) return;
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