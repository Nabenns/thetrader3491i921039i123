<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TheTrader.id') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background-dark text-white selection:bg-primary selection:text-white">
        <div class="min-h-screen flex flex-col">
            <!-- Navbar -->
            <nav 
                x-data="{ scrolled: false, mobileMenuOpen: false }" 
                @scroll.window="scrolled = (window.pageYOffset > 20)"
                :class="scrolled ? 'glass border-b border-white/10 py-2' : 'bg-transparent border-transparent py-4'"
                class="fixed w-full z-50 transition-all duration-300"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="text-2xl font-bold group flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
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
                    class="md:hidden glass border-t border-white/10 absolute w-full"
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
            <main class="flex-grow pt-16">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-black/30 border-t border-white/10 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-gray-400">
                        &copy; {{ date('Y') }} TheTrader.id. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
