<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('academy.index') }}" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Academy
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-100 md:ml-2 truncate max-w-[200px] sm:max-w-xs">{{ $video->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Video Player -->
                <div class="bg-background-dark rounded-2xl overflow-hidden shadow-2xl border border-white/10 aspect-video w-full relative group">
                    <div class="absolute inset-0 bg-black">
                        @if(Str::contains($video->video_url, ['<iframe', '<div']))
                            {!! $video->video_url !!}
                        @else
                            <iframe 
                                src="{{ $video->video_url }}" 
                                class="w-full h-full" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                            ></iframe>
                        @endif
                    </div>
                </div>

                <!-- Video Details -->
                <div class="glass rounded-2xl p-6 sm:p-8 border border-white/10">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-4">{{ $video->title }}</h1>
                    <div class="prose prose-invert prose-lg max-w-none text-gray-300">
                        {!! $video->description !!}
                    </div>
                </div>
            </div>

            <!-- Sidebar / Other Videos -->
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-white px-1">Video Lainnya</h3>
                <div class="space-y-4">
                    @forelse($otherVideos as $otherVideo)
                        <a href="{{ route('academy.show', $otherVideo) }}" class="block group">
                            <div class="glass rounded-xl overflow-hidden border border-white/10 hover:border-primary/50 transition-all duration-300 flex gap-4 p-3 items-center">
                                <!-- Thumbnail -->
                                <div class="relative w-32 aspect-video rounded-lg overflow-hidden flex-shrink-0 bg-black/50">
                                    @if($otherVideo->thumbnail)
                                        <img src="{{ Storage::url($otherVideo->thumbnail) }}" alt="{{ $otherVideo->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-white/5">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif
                                    <!-- Play Icon Overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/30">
                                        <div class="w-8 h-8 rounded-full bg-primary/90 flex items-center justify-center text-white shadow-lg">
                                            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-white group-hover:text-primary transition-colors line-clamp-2">{{ $otherVideo->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">Video {{ $loop->iteration }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">
                            Tidak ada video lain.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
