@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border border-white/10 text-white focus:border-primary focus:ring-primary focus:ring-1 rounded-xl shadow-sm transition-all duration-300 placeholder-gray-500 disabled:opacity-50 disabled:cursor-not-allowed py-3 px-4']) }}>
