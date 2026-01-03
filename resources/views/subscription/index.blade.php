<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                Pilih Paket Langganan
            </h2>
            <p class="mt-4 text-xl text-gray-400">
                Dapatkan akses penuh ke sinyal trading premium dan edukasi eksklusif.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($packages as $package)
                <div class="glass rounded-2xl border border-white/10 p-8 flex flex-col relative overflow-hidden group hover:border-primary/50 transition-colors duration-300">
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold text-white">{{ $package->name }}</h3>
                        <p class="mt-4 flex items-baseline text-white">
                            <span class="text-4xl font-extrabold tracking-tight">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            <span class="ml-1 text-xl font-medium text-gray-400">
                                @if($package->is_lifetime)
                                    /Lifetime
                                @else
                                    /{{ $package->duration_in_days }} hari
                                @endif
                            </span>
                        </p>
                        <p class="mt-2 text-sm text-gray-400">{{ $package->description }}</p>
                    </div>

                    <ul role="list" class="mt-6 space-y-4 flex-1">
                        @if($package->features)
                            @foreach($package->features as $feature)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-300">{{ $feature }}</p>
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <div class="mt-8">
                        <a href="{{ route('checkout', $package->slug) }}" class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-3 px-6 rounded-xl transition-colors duration-300 shadow-lg shadow-primary/20">
                            Pilih Paket
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-400">Belum ada paket langganan yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-dashboard-layout>
