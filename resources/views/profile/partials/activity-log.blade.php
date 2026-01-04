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
            <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($activities as $activity)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ ucfirst(str_replace('_', ' ', $activity->description)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $activity->properties['ip'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white/5 rounded-xl p-6 border border-white/10 text-center">
                <p class="text-gray-400">No recent activity found.</p>
            </div>
        @endif
    </div>
</section>
