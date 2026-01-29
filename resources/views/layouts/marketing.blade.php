<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TheTrader.id') }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js'])
        <style>
            @keyframes marquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee {
                animation: marquee 30s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-background-dark text-white selection:bg-primary selection:text-white">
        <!-- Custom Live Ticker -->
        <div class="fixed top-0 z-[60] w-full bg-background-dark/80 backdrop-blur-md border-b border-white/5 h-12 flex items-center overflow-hidden" 
             x-data="{
                 prices: [
                     { symbol: 'BTC', price: '...', change: 0 },
                     { symbol: 'ETH', price: '...', change: 0 },
                     { symbol: 'XAU', price: '...', change: 0 }, // PAXG as Gold
                     { symbol: 'EUR', price: '...', change: 0 },
                     { symbol: 'GBP', price: '...', change: 0 }
                 ],
                 async fetchPrices() {
                     try {
                         // Fetch from Binance (Free & Public)
                         const res = await fetch('https://api.binance.com/api/v3/ticker/24hr?symbols=[%22BTCUSDT%22,%22ETHUSDT%22,%22PAXGUSDT%22,%22EURUSDT%22,%22GBPUSDT%22]');
                         const data = await res.json();
                         
                         const map = {
                             'BTCUSDT': 'BTC',
                             'ETHUSDT': 'ETH',
                             'PAXGUSDT': 'XAU',
                             'EURUSDT': 'EUR',
                             'GBPUSDT': 'GBP'
                         };

                         this.prices = data.map(item => ({
                             symbol: map[item.symbol],
                             price: parseFloat(item.lastPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                             change: parseFloat(item.priceChangePercent).toFixed(2)
                         }));
                     } catch (e) {
                         console.error('Ticker error:', e);
                     }
                 },
                 init() {
                     this.fetchPrices();
                     setInterval(() => this.fetchPrices(), 5000); // Update every 5s
                 }
             }">
            
            <!-- Infinite Scroll Container -->
            <div class="flex animate-marquee whitespace-nowrap">
                <!-- Loop twice for seamless scroll -->
                <template x-for="i in 2">
                    <div class="flex items-center gap-8 mx-4">
                        <template x-for="item in prices" :key="item.symbol + i">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-300" x-text="item.symbol"></span>
                                <span class="text-sm font-mono" x-text="item.price"></span>
                                <span class="text-xs font-medium px-1.5 py-0.5 rounded"
                                      :class="item.change >= 0 ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400'">
                                    <span x-text="item.change >= 0 ? '+' : ''"></span><span x-text="item.change + '%'"></span>
                                </span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div class="min-h-screen flex flex-col pt-12"> <!-- Added pt-12 to account for ticker height -->
            <!-- Navbar -->
            <nav 
                x-data="{ scrolled: false, mobileMenuOpen: false }" 
                @scroll.window="scrolled = (window.pageYOffset > 20)"
                :class="scrolled ? 'glass border-b border-white/10 py-2 top-12' : 'bg-transparent border-transparent py-4 top-12'"
                class="fixed w-full z-50 transition-all duration-300"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="text-2xl font-bold group flex items-center gap-2">
                                <div class="w-8 h-8 flex items-center justify-center text-white group-hover:rotate-12 transition-transform duration-300 overflow-hidden">
                                    <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo" class="w-full h-full object-contain" />
                                </div>
                                <span class="text-gradient">TheTrader.id</span>
                            </a>
                        </div>

                        <!-- Desktop Menu -->
                        <div class="hidden md:flex items-center space-x-8">
                            <a href="#features" class="text-gray-300 hover:text-primary transition font-medium">Fitur</a>
                            <a href="#pricing" class="text-gray-300 hover:text-primary transition font-medium">Harga</a>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-300 hover:text-primary transition font-medium">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-300 hover:text-primary transition font-medium">Masuk</a>
                                <a href="{{ route('register') }}" class="bg-primary hover:bg-secondary text-white px-5 py-2.5 rounded-xl font-semibold transition shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-0.5">
                                    Daftar Sekarang
                                </a>
                            @endauth
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="flex md:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-gray-300 hover:text-white focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div 
                    x-show="mobileMenuOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="md:hidden glass bg-background-dark/90 border-t border-white/10 absolute w-full"
                    x-cloak
                >
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <a href="#features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Fitur</a>
                        <a href="#pricing" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Harga</a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Masuk</a>
                            <a href="{{ route('register') }}" class="block w-full text-center mt-4 bg-primary hover:bg-secondary text-white px-5 py-3 rounded-xl font-bold transition">
                                Daftar Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <main class="flex-grow pt-16 relative z-10 bg-background-dark mb-[300px] shadow-2xl">
                {{ $slot }}
            </main>

            <!-- Footer (Curtain Effect) -->
            <footer class="fixed bottom-0 left-0 w-full h-[300px] -z-10 bg-background-dark border-t border-white/5 flex items-center justify-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex items-center justify-center opacity-80">
                                <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo" class="w-full h-full object-contain" />
                            </div>
                            <span class="font-semibold text-gray-300 text-xl">TheTrader.id</span>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            &copy; {{ date('Y') }} TheTrader.id. All rights reserved.
                        </div>
                        
                        <div class="flex gap-6">
                            <a href="#" class="text-gray-500 hover:text-white transition-colors">Terms</a>
                            <a href="#" class="text-gray-500 hover:text-white transition-colors">Privacy</a>
                            <a href="#" class="text-gray-500 hover:text-white transition-colors">Contact</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
