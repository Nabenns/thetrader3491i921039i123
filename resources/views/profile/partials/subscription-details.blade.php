<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Subscription Details') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Manage your subscription and billing information.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @php
            $subscription = $user->subscriptions()->latest()->first();
        @endphp

        @if($subscription)
            <!-- Subscription Card -->
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-900 to-black border border-white/10 shadow-2xl group">
                <!-- Background Effects -->
                <div
                    class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-primary/20 rounded-full blur-3xl group-hover:bg-primary/30 transition-colors duration-1000">
                </div>
                <div
                    class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-secondary/20 rounded-full blur-3xl group-hover:bg-secondary/30 transition-colors duration-1000">
                </div>

                <div class="relative p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $subscription->status === 'active' ? 'bg-green-500 text-black' : 'bg-red-500 text-white' }}">
                                    {{ $subscription->status }}
                                </span>
                                <span class="text-sm text-gray-400 font-mono">#{{ substr($subscription->id, 0, 8) }}</span>
                            </div>
                            <h3 class="text-4xl font-bold text-white mb-2">{{ $subscription->package->name }}</h3>
                            <p class="text-gray-400">
                                @if($subscription->ends_at)
                                    Valid until <span
                                        class="text-white font-semibold">{{ $subscription->ends_at->format('d F Y') }}</span>
                                @else
                                    <span class="text-primary font-bold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Lifetime Access
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Price Plan</p>
                            <p class="text-3xl font-bold text-white">Rp
                                {{ number_format($subscription->package->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">per {{ $subscription->package->duration }} days</p>
                        </div>
                    </div>

                    <!-- Features Divider -->
                    <div class="my-8 border-t border-white/10"></div>

                    <!-- Active Features Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 text-sm text-gray-300">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span>Unlimited Journal Entries</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-300">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span>Advanced Analytics Access</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-300">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span>Strategy Management</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-300">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span>Market Webinars</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 text-center">
                <p class="text-gray-400">You don't have an active subscription.</p>
                <a href="{{ route('dashboard') }}#pricing"
                    class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                    Upgrade Now
                </a>
            </div>
        @endif
    </div>
</section>