<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12" x-data="{ showTopicModal: false }">
        
        <!-- Hero Section (Upcoming Webinar) -->
        @if($upcomingWebinar)
        <div class="relative rounded-3xl overflow-hidden min-h-[450px] flex items-center group"
             x-data="{
                timeLeft: { days: 0, hours: 0, minutes: 0, seconds: 0 },
                targetDate: new Date('{{ $upcomingWebinar->schedule->format('Y-m-d H:i:s') }}').getTime(),
                timerInterval: null,
                init() {
                    this.updateTimer();
                    this.timerInterval = setInterval(() => this.updateTimer(), 1000);
                },
                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.targetDate - now;

                    if (distance < 0) {
                        clearInterval(this.timerInterval);
                        this.timeLeft = { days: 0, hours: 0, minutes: 0, seconds: 0 };
                        return;
                    }

                    this.timeLeft.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    this.timeLeft.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.timeLeft.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    this.timeLeft.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                }
             }">
            <!-- Background -->
            <div class="absolute inset-0">
                @if($upcomingWebinar->thumbnail)
                    <img src="{{ Storage::url($upcomingWebinar->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Webinar Hero">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-secondary-900 via-gray-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 px-8 md:px-12 max-w-4xl w-full">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/20 border border-secondary/30 backdrop-blur-md mb-6">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                    </span>
                    <span class="text-xs font-bold text-secondary uppercase tracking-wider">Upcoming Live</span>
                </div>
                
                <h1 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight">
                    {{ $upcomingWebinar->title }}
                </h1>
                
                <div class="text-gray-400 text-lg mb-8 max-w-xl leading-relaxed line-clamp-2 prose prose-invert">
                    {!! $upcomingWebinar->description ?? 'Jangan lewatkan sesi analisis pasar live bersama expert trader kami. Siapkan pertanyaan Anda!' !!}
                </div>

                <!-- Countdown Timer -->
                <div class="grid grid-cols-4 gap-4 max-w-lg mb-8">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-3 text-center">
                        <span class="block text-3xl font-bold text-white" x-text="timeLeft.days">0</span>
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Hari</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-3 text-center">
                        <span class="block text-3xl font-bold text-white" x-text="timeLeft.hours">0</span>
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Jam</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-3 text-center">
                        <span class="block text-3xl font-bold text-white" x-text="timeLeft.minutes">0</span>
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Menit</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-3 text-center">
                        <span class="block text-3xl font-bold text-secondary" x-text="timeLeft.seconds">0</span>
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Detik</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    @if($upcomingWebinar->link)
                        <a href="{{ $upcomingWebinar->link }}" target="_blank" class="px-8 py-3 bg-secondary hover:bg-secondary-600 text-white font-bold rounded-xl shadow-lg shadow-secondary/25 transition-all hover:-translate-y-1 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Join Webinar
                        </a>
                    @else
                        <button disabled class="px-8 py-3 bg-gray-600 text-gray-300 font-bold rounded-xl cursor-not-allowed flex items-center gap-2">
                            Link Belum Tersedia
                        </button>
                    @endif
                    
                    <button @click="showTopicModal = true" class="px-8 py-3 glass hover:bg-white/10 text-white font-semibold rounded-xl transition-all hover:-translate-y-1 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Request Topik
                    </button>
                </div>
            </div>
        </div>
        @else
        <!-- Fallback Hero if no upcoming webinar -->
        <div class="relative rounded-3xl overflow-hidden min-h-[300px] flex items-center bg-gradient-to-r from-gray-900 to-black border border-white/10">
            <div class="relative z-10 px-8 md:px-12 max-w-3xl">
                <h1 class="text-3xl md:text-5xl font-black text-white mb-4">Market Webinar Archive</h1>
                <p class="text-gray-400 text-lg mb-6">
                    Belum ada jadwal webinar mendatang. Tonton rekaman webinar sebelumnya untuk mempertajam analisis Anda.
                </p>
                <button @click="showTopicModal = true" class="px-6 py-3 bg-secondary hover:bg-secondary-600 text-white font-bold rounded-xl shadow-lg shadow-secondary/25 transition-all hover:-translate-y-1 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    Request Topik Webinar
                </button>
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-8 bg-secondary rounded-full"></span>
                    Rekaman Webinar
                </h2>
            </div>

            <!-- Video Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($marketWebinarVideos as $video)
                    <div class="video-item group relative bg-black/20 border border-white/5 rounded-2xl overflow-hidden hover:border-secondary/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-secondary/5">
                        <!-- Thumbnail -->
                        <div class="aspect-video relative overflow-hidden">
                            @if($video->thumbnail)
                                <img src="{{ Storage::url($video->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $video->title }}">
                            @else
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @endif
                            
                            <a href="{{ route('academy.show', $video) }}" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                                <div class="w-14 h-14 rounded-full bg-secondary/90 flex items-center justify-center text-white shadow-lg transform scale-50 group-hover:scale-100 transition-transform duration-300">
                                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </a>

                            <div class="absolute bottom-3 right-3 px-2 py-1 bg-black/60 backdrop-blur-md rounded-md text-xs font-bold text-white border border-white/10">
                                {{ $video->duration ?? '60:00' }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-secondary/10 text-secondary border border-secondary/20">
                                    Webinar
                                </span>
                                <span class="text-xs text-gray-500">• {{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 group-hover:text-secondary transition-colors">
                                <a href="{{ route('academy.show', $video) }}">{{ $video->title }}</a>
                            </h3>
                            
                            <p class="text-sm text-gray-400 line-clamp-2 mb-4">
                                {{ $video->description ? Str::limit(strip_tags($video->description), 100) : 'Analisis pasar mendalam...' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-6">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Belum ada rekaman webinar</h3>
                        <p class="text-gray-400">Nantikan webinar kami selanjutnya!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Request Topic Modal -->
        <div x-show="showTopicModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="showTopicModal = false"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-[#1A1D24] border border-white/10 p-6 text-left align-middle shadow-xl transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Request Topik Webinar</h3>
                        <button @click="showTopicModal = false" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('market-webinar.topic.submit') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="topic" class="block text-sm font-medium text-gray-300 mb-2">Topik yang diinginkan</label>
                            <textarea name="topic" id="topic" rows="4" required
                                class="w-full bg-black/30 border border-white/10 rounded-xl p-4 text-white placeholder-gray-500 focus:border-secondary focus:ring-1 focus:ring-secondary transition-all resize-none"
                                placeholder="Contoh: Cara analisa fundamental untuk pemula..."></textarea>
                            <p class="mt-2 text-xs text-gray-500">Topik Anda akan direview oleh tim kami untuk webinar selanjutnya.</p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showTopicModal = false" class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-secondary hover:bg-secondary-600 text-white font-bold rounded-lg shadow-lg shadow-secondary/20 transition-all hover:-translate-y-1">
                                Kirim Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Toast -->
        @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 px-6 py-3 bg-green-500 rounded-xl text-white font-medium shadow-2xl flex items-center gap-3 z-50 animate-bounce-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        @endif
    </div>
</x-dashboard-layout>
