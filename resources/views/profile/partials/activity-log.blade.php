<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Activity Log') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Recent activity on your account.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @php
            $activities = $user->actions()->latest()->take(10)->get();
        @endphp

        @if($activities->count() > 0)
            <!-- Timeline Container -->
            <div
                class="relative pl-8 space-y-8 before:absolute before:inset-0 before:ml-4 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-primary before:via-white/10 before:to-transparent">
                @foreach($activities as $activity)
                    @php
                        $icon = match (true) {
                            str_contains($activity->description, 'login') => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
                            str_contains($activity->description, 'update') => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>',
                            str_contains($activity->description, 'delete') => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>',
                            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        };

                        $colorClass = match (true) {
                            str_contains($activity->description, 'login') => 'text-green-400 bg-green-400/10 border-green-400/20',
                            str_contains($activity->description, 'delete') => 'text-red-400 bg-red-400/10 border-red-400/20',
                            default => 'text-primary bg-primary/10 border-primary/20'
                        };
                    @endphp

                    <div class="relative group">
                        <!-- Connector Line -->
                        <span
                            class="absolute -left-10 top-1 h-6 w-6 rounded-full border-2 {{ $colorClass }} bg-gray-900 flex items-center justify-center ring-4 ring-gray-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                        </span>

                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between glass p-4 rounded-xl border border-white/5 hover:border-white/10 transition-colors">
                            <div>
                                <h4 class="text-sm font-bold text-white mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $activity->description)) }}</h4>
                                <p class="text-xs text-gray-400">IP: {{ $activity->properties['ip'] ?? 'Unknown' }} &bull;
                                    {{ $activity->properties['agent'] ?? 'Browser' }}</p>
                            </div>
                            <div class="mt-2 sm:mt-0 text-xs font-mono text-gray-500">
                                {{ $activity->created_at->format('d M Y, H:i') }}
                                <span
                                    class="block sm:inline text-gray-600">({{ $activity->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 text-center">
                <p class="text-gray-400">No recent activity found.</p>
            </div>
        @endif
    </div>
</section>