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
            <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $subscription->package->name }}</h3>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium {{ $subscription->status === 'active' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                            @if($subscription->ends_at)
                                <span class="text-sm text-gray-400">Expires on {{ $subscription->ends_at->format('d M Y') }}</span>
                            @else
                                <span class="text-sm text-gray-400">Lifetime Access</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($subscription->package->price, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-400">{{ $subscription->package->duration }} Days</p>
                    </div>
                </div>

                @if($subscription->status === 'active')
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <p class="text-sm text-gray-400">
                            Your subscription is currently active. You have full access to all premium features.
                        </p>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 text-center">
                <p class="text-gray-400">You don't have an active subscription.</p>
                <a href="{{ route('dashboard') }}#pricing" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                    Upgrade Now
                </a>
            </div>
        @endif
    </div>
</section>
