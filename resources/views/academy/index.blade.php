<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Academy</h2>
            <p class="mt-2 text-gray-400">Pelajari strategi trading dan analisis pasar dari video eksklusif kami.</p>
        </div>

        <!-- Edukasi Video Section -->
        <div class="mb-12">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                <span class="w-2 h-8 bg-primary rounded-full mr-3"></span>
                Edukasi Video
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($educationVideos as $video)
                    <x-video-card :video="$video" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-white">Belum ada video edukasi</h3>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Zoom Recap Section -->
        <div>
            <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                <span class="w-2 h-8 bg-success rounded-full mr-3"></span>
                Zoom Recap
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($zoomRecapVideos as $video)
                    <x-video-card :video="$video" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-white">Belum ada video recap</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard-layout>
