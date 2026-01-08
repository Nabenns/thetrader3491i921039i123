@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="glass-card p-6 rounded-2xl relative overflow-hidden group">
    <div class="flex justify-between items-start mb-4">
        <div>
            <p class="text-gray-400 text-sm font-medium">{{ $title }}</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $value }}</h3>
        </div>
        <div class="p-3 bg-{{ $color }}/10 rounded-xl text-{{ $color }} group-hover:scale-110 transition-transform duration-300">
            {!! $icon !!}
        </div>
    </div>
</div>
