<x-dashboard-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Transaction History</h2>
                <p class="text-gray-400 mt-1">View and manage your payment history.</p>
            </div>
        </div>

        <div class="glass rounded-2xl border border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Transaction ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">
                                    #{{ $transaction->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                    {{ $transaction->package->name ?? 'Unknown Package' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $transaction->status === 'expired' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($transaction->status === 'paid')
                                        <a href="{{ route('invoice.download', $transaction) }}" class="text-primary hover:text-primary/80 transition-colors flex items-center justify-end gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Invoice
                                        </a>
                                    @else
                                        <span class="text-gray-500 cursor-not-allowed">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-white/10">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-dashboard-layout>
