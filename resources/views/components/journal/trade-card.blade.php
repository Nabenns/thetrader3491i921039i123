@props(['journal'])

<div class="glass-card p-4 rounded-xl group relative cursor-pointer"
    @click="$dispatch('open-trade-modal', { id: {{ $journal->id }} }); tradeModalOpen = true">
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full flex items-center justify-center {{ $journal->type === 'buy' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($journal->type === 'buy')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18">
                        </path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    @endif
                </svg>
            </div>
            <div>
                <h4 class="text-white font-bold">{{ $journal->pair }}</h4>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="uppercase">{{ $journal->type }}</span>
                    <span>•</span>
                    <span>{{ $journal->open_date->format('d M H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="text-right">
            <div class="font-bold {{ $journal->pnl > 0 ? 'text-green-400' : ($journal->pnl < 0 ? 'text-red-400' : 'text-gray-400') }}"
                x-text="(currency === 'USD' && {{ $journal->pnl }} > 0 ? '+' : '') + formatMoney({{ $journal->pnl }})">
            </div>
            <div class="text-xs text-gray-500">
                {{ $journal->pips }} pips
            </div>
        </div>
    </div>

    <div class="mt-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if($journal->screenshot)
                <span class="text-[10px] px-2 py-1 rounded bg-blue-500/20 text-blue-400 border border-blue-500/20">
                    IMG
                </span>
            @endif

            @if($journal->tradingStrategy)
                <span class="text-[10px] px-2 py-1 rounded border uppercase"
                    style="background-color: {{ $journal->tradingStrategy->color }}10; color: {{ $journal->tradingStrategy->color }}; border-color: {{ $journal->tradingStrategy->color }}20">
                    {{ $journal->tradingStrategy->name }}
                </span>
            @elseif($journal->strategy)
                <span
                    class="text-[10px] px-2 py-1 rounded bg-gray-500/10 text-gray-400 border border-gray-500/20 uppercase">
                    {{ $journal->strategy }}
                </span>
            @endif

            <span class="text-[10px] px-2 py-1 rounded bg-white/5 text-gray-400 border border-white/10 uppercase">
                {{ $journal->emotion }}
            </span>
        </div>

        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('journal.edit', $journal) }}"
                class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </path>
                </svg>
            </a>
            <button
                class="p-1.5 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:shadow-lg hover:shadow-purple-500/20 transition-all hover:-translate-y-0.5"
                @click.stop="openShareModal({{ $journal->id }})" title="Flex This Trade">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>