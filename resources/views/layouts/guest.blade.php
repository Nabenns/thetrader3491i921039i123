<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <!-- Scripts -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js', 'resources/js/chart-bg.js'])
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        @livewireStyles
    </head>
    <body class="font-sans text-gray-100 antialiased bg-background-dark selection:bg-primary selection:text-white relative overflow-hidden"
          x-data="{ focusMode: false }"
          @focusin.window="focusMode = true"
          @focusout.window="focusMode = false"
          @click.away="focusMode = false">
        
        <!-- Focus Mode Overlay -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 transition-opacity duration-500 pointer-events-none"
             x-show="focusMode"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Animated Background -->
        <div class="fixed inset-0 bg-gradient-to-br from-background-dark via-[#0F282D] to-background-dark animate-gradient-slow -z-20"></div>
        <div class="fixed inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay -z-10"></div>
        
        <!-- Live Chart Background -->
        <canvas id="chart-bg" class="fixed inset-0 w-full h-full -z-15 opacity-50"></canvas>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-50" style="perspective: 1000px;">
            <div class="mb-8 hero-content text-center">
                <a href="/" class="flex items-center justify-center gap-3 group mb-2">
                    <div class="w-12 h-12 flex items-center justify-center text-white group-hover:rotate-12 transition-transform duration-300 overflow-hidden">
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo" class="w-full h-full object-contain" />
                    </div>
                    <span class="text-3xl font-bold text-gradient">TheTrader.id</span>
                </a>
                <!-- Typing Effect -->
                <div class="h-6 text-gray-400 font-mono text-sm" 
                     x-data="{ 
                         text: '', 
                         phrases: ['Analyze the market.', 'Trade with confidence.', 'Maximize your profit.'],
                         phraseIndex: 0,
                         charIndex: 0,
                         isDeleting: false,
                         type() {
                             const currentPhrase = this.phrases[this.phraseIndex];
                             
                             if (this.isDeleting) {
                                 this.text = currentPhrase.substring(0, this.charIndex - 1);
                                 this.charIndex--;
                             } else {
                                 this.text = currentPhrase.substring(0, this.charIndex + 1);
                                 this.charIndex++;
                             }

                             let speed = 100;
                             if (this.isDeleting) speed /= 2;

                             if (!this.isDeleting && this.charIndex === currentPhrase.length) {
                                 speed = 2000; // Pause at end
                                 this.isDeleting = true;
                             } else if (this.isDeleting && this.charIndex === 0) {
                                 this.isDeleting = false;
                                 this.phraseIndex = (this.phraseIndex + 1) % this.phrases.length;
                                 speed = 500;
                             }

                             setTimeout(() => this.type(), speed);
                         }
                     }" 
                     x-init="type()">
                    <span x-text="text"></span><span class="animate-pulse">|</span>
                </div>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 glass shadow-2xl overflow-hidden sm:rounded-3xl border border-white/10 relative z-50 tilt-card spotlight-card hero-content">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary"></div>
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
