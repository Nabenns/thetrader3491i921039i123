@props(['video'])

@php
    $isPremiumUser = auth()->user()->hasActiveSubscription();
@endphp

<a href="{{ $isPremiumUser ? route('academy.show', $video) : route('subscription.index') }}" class="block group">
    <div class="glass rounded-xl border border-white/10 overflow-hidden hover:border-primary/50 transition-all duration-300 h-full flex flex-col relative">
        <!-- Thumbnail -->
        <div class="relative aspect-video bg-black/50">
            @if($video->thumbnail)
                <img src="{{ Storage::url($video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300 {{ !$isPremiumUser ? 'grayscale' : '' }}">
            @else
                <div class="w-full h-full flex items-center justify-center bg-white/5">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            @endif
            
            <!-- Overlay -->
            <div class="absolute inset-0 flex items-center justify-center">
                @if($isPremiumUser)
                    <div class="w-12 h-12 rounded-full bg-primary/90 flex items-center justify-center text-white shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                    </div>
                @else
                    <div class="w-12 h-12 rounded-full bg-black/80 flex items-center justify-center text-red-500 shadow-lg backdrop-blur-sm border border-red-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start justify-between">
                <h3 class="text-lg font-semibold text-white line-clamp-2 group-hover:text-primary transition-colors">{{ $video->title }}</h3>
                @if(!$isPremiumUser)
                    <span class="ml-2 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-red-500/20 text-red-400 rounded border border-red-500/20">Premium</span>
                @endif
            </div>
            <div class="mt-2 text-sm text-gray-400 line-clamp-2 prose prose-invert prose-sm">
                {!! strip_tags($video->description) !!}
            </div>
        </div>
    </div>
</a>
