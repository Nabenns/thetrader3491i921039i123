<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="academyShow()">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
            <!-- Main Content -->
            <div :class="theaterMode ? 'lg:col-span-3' : 'lg:col-span-2'" class="space-y-6 transition-all duration-500">
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
                    
                    <!-- Theater Mode Toggle -->
                    <button @click="toggleTheater()" class="absolute top-4 right-4 p-2 bg-black/50 backdrop-blur-md rounded-lg text-white opacity-0 group-hover:opacity-100 transition-all hover:bg-primary hover:text-white z-20" title="Theater Mode">
                        <svg x-show="!theaterMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        <svg x-show="theaterMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4M4 16v-4m0 0h4m-4 0l5 5m11-5l-5 5m5-5v4m0-4h-4"></path></svg>
                    </button>
                </div>

                <!-- Video Details & Tabs -->
                <div class="glass rounded-2xl border border-white/10 overflow-hidden">
                    <!-- Header -->
                    <div class="p-6 sm:p-8 border-b border-white/5">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                            <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">{{ $video->title }}</h1>
                            <div class="flex items-center gap-3">
                                <button @click="toggleComplete()" 
                                    :class="isCompleted ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl border transition-all group">
                                    <svg x-show="!isCompleted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <svg x-show="isCompleted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm font-medium" x-text="isCompleted ? 'Completed' : 'Mark Complete'"></span>
                                </button>
                                
                                <button @click="toggleWatchlist({{ $video->id }})" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all group active:scale-95">
                                    <svg :class="isInWatchlist ? 'text-primary' : 'text-gray-400 group-hover:text-primary'" class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $video->duration ?? '10:00' }}
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $video->created_at->format('d M Y') }}
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">
                                    {{ $video->category === 'education' ? 'Edukasi' : 'Zoom Recap' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="flex border-b border-white/5 bg-black/20">
                        <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'text-primary border-primary bg-white/5' : 'text-gray-400 border-transparent hover:text-white hover:bg-white/5'"
                            class="px-6 py-3 text-sm font-medium border-b-2 transition-all">
                            Overview
                        </button>
                        <button @click="activeTab = 'notes'" 
                            :class="activeTab === 'notes' ? 'text-primary border-primary bg-white/5' : 'text-gray-400 border-transparent hover:text-white hover:bg-white/5'"
                            class="px-6 py-3 text-sm font-medium border-b-2 transition-all flex items-center gap-2">
                            Notes
                            <span class="px-1.5 py-0.5 rounded-full bg-white/10 text-[10px] text-gray-300">Personal</span>
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6 sm:p-8 min-h-[300px]">
                        <!-- Overview Tab -->
                        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="prose prose-invert prose-lg max-w-none text-gray-300">
                                {!! $video->description !!}
                            </div>
                        </div>

                        <!-- Notes Tab -->
                        <div x-show="activeTab === 'notes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-white">Catatan Pribadi</h3>
                                    <span class="text-xs text-gray-500">Hanya Anda yang bisa melihat ini</span>
                                </div>
                                <textarea x-model="note" rows="8" 
                                    class="w-full bg-black/30 border border-white/10 rounded-xl p-4 text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary transition-all resize-none"
                                    placeholder="Tulis poin penting dari video ini..."></textarea>
                                <div class="flex justify-end">
                                    <button @click="saveNote()" :disabled="isSavingNote" class="px-6 py-2 bg-primary hover:bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary/25 transition-all hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <svg x-show="isSavingNote" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span x-text="isSavingNote ? 'Saving...' : 'Simpan Catatan'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar / Other Videos -->
            <div :class="theaterMode ? 'hidden' : 'block'" class="space-y-6 lg:sticky lg:top-24 h-fit transition-all duration-500">
                <h3 class="text-xl font-bold text-white px-1 flex items-center gap-2 border-l-4 border-secondary pl-3">
                    Video Lainnya
                </h3>
                <div class="space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto custom-scrollbar pr-2">
                    @forelse($otherVideos as $otherVideo)
                        <a href="{{ route('academy.show', $otherVideo) }}" class="block group">
                            <div class="glass rounded-xl overflow-hidden border border-white/10 hover:border-primary/50 transition-all duration-300 flex gap-4 p-3 items-center hover:bg-white/5">
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
                                        <div class="w-8 h-8 rounded-full bg-primary/90 flex items-center justify-center text-white shadow-lg scale-75 group-hover:scale-100 transition-transform">
                                            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-white group-hover:text-primary transition-colors line-clamp-2">{{ $otherVideo->title }}</h4>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-gray-400 border border-white/5">{{ $otherVideo->duration ?? '10:00' }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm glass rounded-xl border border-white/5 border-dashed">
                            Tidak ada video lain.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>

    <script>
        function academyShow() {
            return {
                theaterMode: false,
                activeTab: 'overview',
                isCompleted: {{ $progress && $progress->pivot->is_completed ? 'true' : 'false' }},
                note: '{{ $note ? addslashes($note->content) : '' }}',
                isSavingNote: false,
                isInWatchlist: {{ auth()->user()->watchlist()->where('video_id', $video->id)->exists() ? 'true' : 'false' }},
                
                toggleTheater() {
                    this.theaterMode = !this.theaterMode;
                },
                
                toggleComplete() {
                    fetch('{{ route('academy.complete', $video) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isCompleted = data.status === 'completed';
                        this.showToast(data.message, data.status === 'completed' ? 'success' : 'info');
                    });
                },
                
                saveNote() {
                    this.isSavingNote = true;
                    fetch('{{ route('academy.note.save', $video) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        },
                        body: JSON.stringify({ content: this.note })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingNote = false;
                        this.showToast(data.message, 'success');
                    });
                },

                toggleWatchlist(videoId) {
                    fetch(`/academy/${videoId}/watchlist`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.isInWatchlist = data.status === 'added';
                        this.showToast(data.message, data.status === 'added' ? 'success' : 'info');
                    })
                    .catch(error => console.error('Error:', error));
                },

                showToast(message, type = 'success') {
                    const toast = document.createElement('div');
                    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-blue-500';
                    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-xl text-white font-medium shadow-2xl transform transition-all duration-500 translate-y-10 opacity-0 z-50 flex items-center gap-3 ${bgColor}`;
                    toast.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"></path></svg>
                        ${message}
                    `;
                    document.body.appendChild(toast);
                    requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));
                    setTimeout(() => {
                        toast.classList.add('translate-y-10', 'opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }, 3000);
                }
            }
        }
    </script>
</x-dashboard-layout>
