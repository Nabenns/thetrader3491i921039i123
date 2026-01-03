<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans text-gray-100 antialiased bg-background-dark selection:bg-primary selection:text-white relative overflow-hidden">
        <!-- Animated Background -->
        <div class="fixed inset-0 bg-gradient-to-br from-background-dark via-[#0F282D] to-background-dark animate-gradient-slow -z-20"></div>
        <div class="fixed inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay -z-10"></div>
        
        <!-- Floating Blobs -->
        <div class="fixed top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-blob mix-blend-screen -z-10"></div>
        <div class="fixed top-1/3 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-blob animation-delay-2000 mix-blend-screen -z-10"></div>
        <div class="fixed -bottom-32 left-1/3 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-blob animation-delay-4000 mix-blend-screen -z-10"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-white group-hover:rotate-12 transition-transform duration-300 shadow-lg shadow-primary/20">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-3xl font-bold text-gradient">TheTrader.id</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 glass shadow-2xl overflow-hidden sm:rounded-3xl border border-white/10 relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary"></div>
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
