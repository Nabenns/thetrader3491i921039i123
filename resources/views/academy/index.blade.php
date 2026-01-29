<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12" 
         x-data="{ 
            activeTab: 'all', 
            search: '',
            filteredCount: 0,
            init() {
                this.$watch('activeTab', () => this.updateVisibility());
                this.$watch('search', () => this.updateVisibility());
                // Initial check after DOM is ready
                this.$nextTick(() => this.updateVisibility());
            },
            updateVisibility() {
                let count = 0;
                const searchLower = this.search.toLowerCase();
                
                document.querySelectorAll('.video-item').forEach(el => {
                    const title = el.dataset.title;
                    const category = el.dataset.category;
                    
                    const matchesTab = this.activeTab === 'all' || 
                                     (this.activeTab === 'education' && category === 'education') ||
                                     (this.activeTab === 'zoom' && category === 'zoom_recap');
                                     
                    const matchesSearch = title.includes(searchLower);
                    
                    if (matchesTab && matchesSearch) {
                        el.style.display = 'block';
                        count++;
                    } else {
                        el.style.display = 'none';
                    }
                });
                
                this.filteredCount = count;
            }
         }">
        
        <!-- Hero Section -->
        @if($featuredVideos->count() > 0)
        <div class="relative rounded-3xl overflow-hidden min-h-[400px] group" 
             x-data="{ 
                activeSlide: 0,
                slides: {{ $featuredVideos->count() }},
                autoplayInterval: null,
                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slides;
                    }, 5000);
                },
                stopAutoplay() {
                    clearInterval(this.autoplayInterval);
                }
             }"
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()">
            
            <!-- Slides -->
            @foreach($featuredVideos as $index => $featuredVideo)
            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                 x-show="activeSlide === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-700"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: {{ $index === 0 ? 'block' : 'none' }}">
                
                <!-- Background Image with Overlay -->
                <div class="absolute inset-0">
                    @if($featuredVideo->thumbnail)
                        <img src="{{ Storage::url($featuredVideo->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" alt="Academy Hero">
                    @else
                        <div class="w-full h-full bg-gradient-to-r from-gray-900 to-black"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 px-8 md:px-12 max-w-3xl h-full flex flex-col justify-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 border border-primary/30 backdrop-blur-md mb-6 w-fit">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        <span class="text-xs font-bold text-primary uppercase tracking-wider">Featured</span>
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-4 leading-tight line-clamp-3">
                        {{ $featuredVideo->title }}
                    </h1>
                    
                    <p class="text-gray-400 text-base sm:text-lg mb-8 max-w-xl leading-relaxed line-clamp-2 md:line-clamp-3">
                        {{ Str::limit(strip_tags($featuredVideo->description), 150) }}
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('academy.show', $featuredVideo) }}" class="px-8 py-3 bg-primary hover:bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary/25 transition-all hover:-translate-y-1 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mulai Belajar
                        </a>
                        <button @click="$el.scrollIntoView({behavior: 'smooth', block: 'start'})" class="px-8 py-3 glass hover:bg-white/10 text-white font-semibold rounded-xl transition-all hover:-translate-y-1">
                            Lihat Katalog
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Indicators -->
            @if($featuredVideos->count() > 1)
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
                @foreach($featuredVideos as $index => $video)
                <button @click="activeSlide = {{ $index }}" 
                        :class="activeSlide === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-white/30 hover:bg-white/50'"
                        class="h-2 rounded-full transition-all duration-300"></button>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            <button @click="activeSlide = (activeSlide - 1 + slides) % slides" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/20 hover:bg-black/40 text-white/50 hover:text-white backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="activeSlide = (activeSlide + 1) % slides" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/20 hover:bg-black/40 text-white/50 hover:text-white backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            @endif
        </div>
        @endif

        <!-- Main Content Area -->
        <div>
            <!-- Tabs & Search -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8 sticky top-0 z-30 py-4 bg-background-dark/80 backdrop-blur-xl border-b border-white/5">
                <!-- Tabs -->
                <div class="flex p-1 bg-black/20 rounded-xl border border-white/5 overflow-x-auto max-w-full">
                    <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'bg-white/10 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                        Semua
                    </button>
                    <button @click="activeTab = 'education'" 
                        :class="activeTab === 'education' ? 'bg-primary/20 text-primary shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        Edukasi
                    </button>
                    <button @click="activeTab = 'zoom'" 
                        :class="activeTab === 'zoom' ? 'bg-secondary/20 text-secondary shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span>
                        Zoom Recap
                    </button>
                </div>

                <!-- Search -->
                <div class="relative w-full md:w-72">
                    <input type="text" x-model="search" placeholder="Cari video..." 
                        class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-primary focus:border-primary transition-all">
                    <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Video Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Education Videos -->
                @foreach($educationVideos as $video)
                    <div class="video-item group relative bg-black/20 border border-white/5 rounded-2xl overflow-hidden hover:border-primary/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-primary/5"
                         data-title="{{ strtolower($video->title) }}"
                         data-category="education">
                        
                        <!-- Thumbnail -->
                        <div class="aspect-video relative overflow-hidden">
                            @if($video->thumbnail)
                                <img src="{{ Storage::url($video->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $video->title }}">
                            @else
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @endif
                            
                            <!-- Completed Badge -->
                            @if($video->videoProgress->isNotEmpty() && $video->videoProgress->first()->pivot->is_completed)
                                <div class="absolute top-3 left-3 z-20 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md flex items-center gap-1 shadow-lg">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Selesai</span>
                                </div>
                            @endif
                            
                            <!-- Overlay Play Button -->
                            <a href="{{ route('academy.show', $video) }}" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                                <div class="w-14 h-14 rounded-full bg-primary/90 flex items-center justify-center text-white shadow-lg transform scale-50 group-hover:scale-100 transition-transform duration-300">
                                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </a>

                            <!-- Duration Badge -->
                            <div class="absolute bottom-3 right-3 px-2 py-1 bg-black/60 backdrop-blur-md rounded-md text-xs font-bold text-white border border-white/10">
                                {{ $video->duration ?? '10:00' }}
                            </div>
                            
                            <!-- Watch Later Button -->
                            <button @click="toggleWatchlist({{ $video->id }})" class="absolute top-3 right-3 p-2 rounded-full bg-black/40 backdrop-blur-md text-white border border-white/10 hover:bg-primary hover:border-primary transition-colors z-20 group/btn" title="Watch Later">
                                <svg class="w-4 h-4 transition-colors {{ $video->watchlist->isNotEmpty() ? 'text-primary fill-current' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">
                                    Edukasi
                                </span>
                                <span class="text-xs text-gray-500">• {{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                <a href="{{ route('academy.show', $video) }}">{{ $video->title }}</a>
                            </h3>
                            
                            <p class="text-sm text-gray-400 line-clamp-2 mb-4">
                                {{ $video->description ? Str::limit(strip_tags($video->description), 100) : 'Pelajari teknik trading terbaru...' }}
                            </p>

                            <div class="flex items-center justify-between pt-4 border-t border-white/5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-[10px] font-bold text-white border border-white/10">
                                        TT
                                    </div>
                                    <span class="text-xs text-gray-400">TheTrader Team</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Zoom Recap Videos -->
                @foreach($zoomRecapVideos as $video)
                    <div class="video-item group relative bg-black/20 border border-white/5 rounded-2xl overflow-hidden hover:border-secondary/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-secondary/5"
                         data-title="{{ strtolower($video->title) }}"
                         data-category="zoom_recap">
                        
                        <!-- Thumbnail -->
                        <div class="aspect-video relative overflow-hidden">
                            @if($video->thumbnail)
                                <img src="{{ Storage::url($video->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $video->title }}">
                            @else
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @endif
                            
                            <!-- Completed Badge -->
                            @if($video->videoProgress->isNotEmpty() && $video->videoProgress->first()->pivot->is_completed)
                                <div class="absolute top-3 left-3 z-20 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md flex items-center gap-1 shadow-lg">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Selesai</span>
                                </div>
                            @endif
                            
                            <a href="{{ route('academy.show', $video) }}" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                                <div class="w-14 h-14 rounded-full bg-secondary/90 flex items-center justify-center text-white shadow-lg transform scale-50 group-hover:scale-100 transition-transform duration-300">
                                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </a>

                            <div class="absolute bottom-3 right-3 px-2 py-1 bg-black/60 backdrop-blur-md rounded-md text-xs font-bold text-white border border-white/10">
                                {{ $video->duration ?? '45:00' }}
                            </div>

                            <button @click="toggleWatchlist({{ $video->id }})" class="absolute top-3 right-3 p-2 rounded-full bg-black/40 backdrop-blur-md text-white border border-white/10 hover:bg-secondary hover:border-secondary transition-colors z-20 group/btn" title="Watch Later">
                                <svg class="w-4 h-4 transition-colors {{ $video->watchlist->isNotEmpty() ? 'text-secondary fill-current' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-secondary/10 text-secondary border border-secondary/20">
                                    Zoom Recap
                                </span>
                                <span class="text-xs text-gray-500">• {{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 group-hover:text-secondary transition-colors">
                                <a href="{{ route('academy.show', $video) }}">{{ $video->title }}</a>
                            </h3>
                            
                            <p class="text-sm text-gray-400 line-clamp-2 mb-4">
                                {{ $video->description ? Str::limit(strip_tags($video->description), 100) : 'Rekaman sesi live trading dan Q&A...' }}
                            </p>

                            <div class="flex items-center justify-between pt-4 border-t border-white/5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-[10px] font-bold text-white border border-white/10">
                                        TT
                                    </div>
                                    <span class="text-xs text-gray-400">TheTrader Team</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty State -->
            <div x-show="filteredCount === 0" class="text-center py-20" style="display: none;">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-6">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Tidak ada video ditemukan</h3>
                <p class="text-gray-400">Coba cari dengan kata kunci lain.</p>
            </div>
        </div>
    </div>

    <script>
        function toggleWatchlist(videoId) {
            fetch(`/academy/${videoId}/watchlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Show toast notification
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-xl text-white font-medium shadow-2xl transform transition-all duration-500 translate-y-10 opacity-0 z-50 flex items-center gap-3 ${data.status === 'added' ? 'bg-green-500' : 'bg-red-500'}`;
                toast.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${data.status === 'added' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"></path></svg>
                    ${data.message}
                `;
                document.body.appendChild(toast);
                
                // Animate in
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-y-10', 'opacity-0');
                });

                // Remove after 3s
                setTimeout(() => {
                    toast.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</x-dashboard-layout>
