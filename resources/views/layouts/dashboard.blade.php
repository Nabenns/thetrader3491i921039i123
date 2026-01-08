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
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-background-dark text-white" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 glass border-r border-white/10 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 border-b border-white/10">
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white group-hover:rotate-12 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <span class="text-xl font-bold text-gradient">TheTrader.id</span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="mt-5 px-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('academy.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('academy.index') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Academy
                    </a>
                    <a href="{{ route('market-webinar.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('market-webinar.index') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Market Webinar
                    </a>
                    <a href="{{ route('journal.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('journal.index') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Trading Journal
                    </a>
                    <a href="{{ route('subscription.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('subscription.index') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Langganan
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/5 hover:text-white rounded-xl transition-colors {{ request()->routeIs('profile.edit') ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profil
                    </a>
                </nav>
                
                <!-- User Profile Bottom -->
                <div class="absolute bottom-0 w-full p-4 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Header (Mobile) -->
                <header class="lg:hidden flex items-center justify-between h-16 px-4 border-b border-white/10 glass">
                    <button @click="sidebarOpen = true" class="text-gray-300 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <span class="text-lg font-bold text-white">{{ $title ?? 'Dashboard' }}</span>
                    <div class="w-6"></div> <!-- Spacer -->
                </header>

                <!-- Overlay -->
                <div 
                    x-show="sidebarOpen" 
                    @click="sidebarOpen = false"
                    x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                ></div>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @livewire('notifications')
        @livewireScripts
    </body>
</html>
