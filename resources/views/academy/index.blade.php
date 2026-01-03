<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Academy</h2>
            <p class="mt-2 text-gray-400">Pelajari strategi trading dan analisis pasar dari video eksklusif kami.</p>
        </div>

        <!-- Webinars Section -->
        @if($webinars->isNotEmpty())
            <div class="mb-12">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                    <span class="w-2 h-8 bg-primary rounded-full mr-3"></span>
                    Webinar Mendatang
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($webinars as $webinar)
                        @php
                            $canJoin = !$webinar->is_premium || auth()->user()->hasActiveSubscription();
                        @endphp
                        <div class="glass rounded-xl p-6 border border-white/10 relative overflow-hidden group hover:border-primary/50 transition-all duration-300">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <svg class="w-24 h-24 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                            </div>
                            
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 text-xs font-medium bg-primary/20 text-primary rounded-full border border-primary/20">
                                        Live Zoom
                                    </span>
                                    <span class="text-sm text-gray-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $webinar->schedule->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <h4 class="text-lg font-bold text-white mb-2 line-clamp-2">{{ $webinar->title }}</h4>
                                <p class="text-gray-400 text-sm mb-6 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $webinar->schedule->format('H:i') }} WIB
                                </p>
                                
                                @if($canJoin)
                                    <a href="{{ $webinar->link }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 bg-white/10 hover:bg-primary hover:text-white text-white rounded-lg transition-all duration-300 font-medium group-hover:shadow-lg group-hover:shadow-primary/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        Join Webinar
                                    </a>
                                @else
                                    <a href="{{ route('subscription.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-lg transition-all duration-300 font-medium border border-red-500/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Premium Only
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <h3 class="text-xl font-bold text-white mb-6 flex items-center">
            <span class="w-2 h-8 bg-secondary rounded-full mr-3"></span>
            Video Pembelajaran
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($videos as $video)
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
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">Belum ada video</h3>
                    <p class="mt-2 text-gray-400">Video pembelajaran akan segera tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-dashboard-layout>
