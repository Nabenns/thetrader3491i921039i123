<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TheTrader.id') }} - Your Ultimate Trading Journal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/landing.js'])
    
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
        }
        
        .glass-nav {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .text-gradient-primary {
            background: linear-gradient(135deg, #2dd4bf 0%, #0f766e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(45, 212, 191, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased selection:bg-teal-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo" class="w-full h-full object-contain" />
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">TheTrader.id</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-medium bg-white text-black rounded-full hover:bg-gray-200 transition-all transform hover:scale-105">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="hero-glow"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="hero-content max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-teal-400 mb-8">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    New: Shareable Flex Cards Available
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 leading-tight">
                    Master Your Trading <br>
                    <span class="text-gradient-primary">With Precision</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                    The ultimate trading journal designed for serious traders. Track, analyze, and improve your performance with professional-grade tools and analytics.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-teal-500 hover:bg-teal-400 text-black font-semibold rounded-full transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(45,212,191,0.3)]">
                        Start Journaling Free
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium rounded-full transition-all backdrop-blur-sm">
                        Explore Features
                    </a>
                </div>
            </div>

            <!-- Hero Image / Dashboard Preview -->
            <div class="hero-image mt-20 relative max-w-6xl mx-auto">
                <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-blue-600 rounded-2xl blur opacity-20"></div>
                <div class="relative rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-[#0f0f0f]">
                    <img src="{{ asset('dashboard-preview.png') }}" onerror="this.src='https://placehold.co/1200x800/1a1a1a/ffffff?text=Dashboard+Preview'" alt="Dashboard Preview" class="w-full h-auto" />
                    
                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent opacity-60"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 section-title">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Built for <span class="text-gradient-primary">Performance</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto text-lg">Everything you need to analyze your trades and find your edge in the market.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 bg-teal-500/10 rounded-xl flex items-center justify-center mb-6 text-teal-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Advanced Analytics</h3>
                    <p class="text-gray-400 leading-relaxed">Visualize your equity curve, win rate, and profit factor automatically. Identify patterns in your trading behavior.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center mb-6 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Interactive Calendar</h3>
                    <p class="text-gray-400 leading-relaxed">Review your trading days at a glance. Filter trades by date, pair, or outcome with a single click.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center mb-6 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Shareable Flex Cards</h3>
                    <p class="text-gray-400 leading-relaxed">Generate beautiful, branded images of your winning trades to share on social media instantly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-teal-900/10"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10 section-title">
            <h2 class="text-4xl md:text-5xl font-bold mb-8">Ready to Level Up?</h2>
            <p class="text-xl text-gray-400 mb-10">Join thousands of traders who are taking their performance to the next level with TheTrader.id.</p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-white text-black font-bold rounded-full hover:bg-gray-200 transition-all transform hover:scale-105 shadow-xl">
                Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 bg-[#050505]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center opacity-80">
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="Logo" class="w-full h-full object-contain" />
                    </div>
                    <span class="font-semibold text-gray-300">TheTrader.id</span>
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

</body>
</html>
