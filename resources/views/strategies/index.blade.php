<x-dashboard-layout title="Trading Strategies">
    <div class="py-12" x-data="{ 
        editModalOpen: false, 
        strategyToEdit: null,
        openEditModal(strategy) {
            this.strategyToEdit = strategy;
            this.editModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Back Button -->
            <div class="flex items-center">
                <a href="{{ route('journal.index') }}"
                    class="group flex items-center text-gray-400 hover:text-white transition-colors">
                    <div class="mr-2 p-1 rounded-lg bg-white/5 group-hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </div>
                    <span>Back to Journal</span>
                </a>
            </div>

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 border-b border-white/10 pb-8">
                <div>
                    <h2
                        class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-white mb-2">
                        Strategies
                    </h2>
                    <p class="text-gray-400">Manage your trading strategies and colors.</p>
                </div>

                <!-- Add Strategy Form (Quick Inline) -->
                <div class="bg-black/20 p-4 rounded-xl border border-white/5">
                    <form action="{{ route('strategies.store') }}" method="POST"
                        class="flex flex-col sm:flex-row gap-4 items-end">
                        @csrf
                        <div>
                            <x-input-label for="name" value="Strategy Name"
                                class="mb-1 text-xs uppercase text-gray-500 font-bold" />
                            <x-text-input id="name" name="name" type="text" placeholder="e.g. Scalping" required
                                class="w-full sm:w-48" />
                        </div>
                        <div>
                            <x-input-label for="color" value="Color"
                                class="mb-1 text-xs uppercase text-gray-500 font-bold" />
                            <div class="relative">
                                <input type="color" name="color" value="#3b82f6"
                                    class="h-10 w-full sm:w-16 rounded-lg border border-gray-700 bg-gray-900 cursor-pointer p-0.5 opacity-0 absolute inset-0 z-10">
                                <div
                                    class="h-10 w-full sm:w-16 rounded-lg border border-gray-700 bg-gray-900 flex items-center justify-center pointer-events-none">
                                    <div class="w-6 h-6 rounded-full border border-white/20"
                                        style="background-color: #3b82f6" id="color-preview"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="btn-primary px-6 py-2 rounded-lg h-10 flex items-center justify-center">
                            <span class="mr-2">+</span> Add
                        </button>
                    </form>
                </div>
            </div>

            <script>
                // Simple script to update color preview
                document.querySelector('input[name="color"]').addEventListener('input', function (e) {
                    document.getElementById('color-preview').style.backgroundColor = e.target.value;
                });
            </script>

            <!-- Strategies Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($strategies as $strategy)
                    <div class="glass p-6 rounded-2xl border border-white/10 relative group">
                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openEditModal({{ $strategy }})"
                                class="p-1.5 text-gray-400 hover:text-white bg-black/40 rounded-lg hover:bg-black/60 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                            </button>
                            <form action="{{ route('strategies.destroy', $strategy) }}" method="POST"
                                onsubmit="return confirm('Are you sure? Linked trades will lose this strategy tag.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-red-400 hover:text-red-300 bg-black/40 rounded-lg hover:bg-black/60 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg"
                                style="background-color: {{ $strategy->color }}20; color: {{ $strategy->color }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ $strategy->name }}</h3>
                        </div>
                        <p class="text-gray-400 text-sm mb-4 min-h-[40px]">
                            {{ $strategy->description ?? 'No description provided.' }}
                        </p>

                        <div class="pt-4 border-t border-white/5 flex justify-between items-center text-xs text-gray-500">
                            <span>Trades: {{ $strategy->tradingJournals->count() }}</span>
                            <span class="font-mono">{{ strtoupper($strategy->color) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        No strategies found. Add one above to get started.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom glass border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-[70]">
                    <form :action="'/strategies/' + strategyToEdit?.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg font-medium text-white">Edit Strategy</h3>

                        <div>
                            <x-input-label for="edit_name" value="Name" />
                            <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full"
                                x-model="strategyToEdit?.name" required />
                        </div>

                        <div>
                            <x-input-label for="edit_desc" value="Description" />
                            <textarea id="edit_desc" name="description" rows="3"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm"
                                x-model="strategyToEdit?.description"></textarea>
                        </div>

                        <div>
                            <x-input-label for="edit_color" value="Color" />
                            <div class="flex items-center gap-2 mt-1">
                                <input type="color" id="edit_color" name="color"
                                    class="h-10 w-10 rounded-lg border border-gray-700 bg-gray-900 cursor-pointer p-1"
                                    x-model="strategyToEdit?.color">
                                <span class="text-gray-400 text-sm font-mono" x-text="strategyToEdit?.color"></span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="editModalOpen = false"
                                class="px-4 py-2 text-gray-400 hover:text-white">Cancel</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>