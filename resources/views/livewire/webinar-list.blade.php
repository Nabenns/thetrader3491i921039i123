<div class="grid md:grid-cols-3 gap-6">
    @foreach($webinars as $webinar)
        <div class="glass rounded-lg overflow-hidden border border-white/10 hover:border-primary/50 transition group">
            <div class="aspect-video bg-gray-800 relative">
                @if($webinar->thumbnail)
                    <img src="{{ Storage::url($webinar->thumbnail) }}" alt="{{ $webinar->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-600">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                @endif
                
                @if($webinar->is_premium)
                    <div class="absolute top-2 right-2 bg-primary text-white text-xs px-2 py-1 rounded">Premium</div>
                @else
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">Free</div>
                @endif
            </div>
            <div class="p-4">
                <div class="text-sm text-gray-400 mb-2">{{ $webinar->schedule->format('d M Y, H:i') }}</div>
                <h3 class="text-lg font-bold mb-2 group-hover:text-primary transition">{{ $webinar->title }}</h3>
                <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $webinar->description }}</p>
                
                <a href="{{ $webinar->link }}" target="_blank" class="block w-full py-2 rounded border border-white/20 text-center hover:bg-white/10 transition">
                    Watch Now
                </a>
            </div>
        </div>
    @endforeach
</div>
