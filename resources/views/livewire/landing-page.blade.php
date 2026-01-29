<div class="relative overflow-hidden">
    <style>
        .spotlight-card {
            position: relative;
            overflow: hidden;
        }
        .spotlight-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(
                800px circle at var(--mouse-x) var(--mouse-y),
                rgba(255, 255, 255, 0.06),
                transparent 40%
            );
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s;
        }
        .spotlight-card:hover::before {
            opacity: 1;
        }
        .tilt-card {
            transform-style: preserve-3d;
        }
    </style>

    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-background-dark via-[#0F282D] to-background-dark animate-gradient-slow"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay"></div>
        
        <!-- Floating Blobs (Parallax) -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-blob mix-blend-screen parallax-bg"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-blob animation-delay-2000 mix-blend-screen parallax-bg"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-blob animation-delay-4000 mix-blend-screen parallax-bg"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 hero-content">
            <h1 class="text-6xl md:text-8xl font-bold mb-8 tracking-tight">
                Kuasai Pasar dengan <br>
                <span class="text-gradient bg-clip-text text-transparent bg-gradient-to-r from-primary via-white to-secondary animate-gradient-x">Presisi & Percaya Diri</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-400 mb-12 max-w-3xl mx-auto leading-relaxed">
                Edukasi trading premium, webinar eksklusif, dan analisis pasar real-time untuk Forex, Saham, dan Crypto.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#pricing" class="magnetic-btn group relative px-8 py-4 bg-primary rounded-xl text-lg font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/50 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-500 -skew-x-12 -translate-x-full"></div>
                    Mulai Belajar
                </a>
                <a href="#features" class="magnetic-btn px-8 py-4 glass rounded-xl text-lg font-bold text-white hover:bg-white/10 transition-all duration-300 border border-white/10 hover:border-white/30">
                    Lihat Fitur
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 section-title">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Kenapa Memilih <span class="text-primary">TheTrader.id</span>?</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">Semua yang Anda butuhkan untuk menjadi trader profitable, ada di sini.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass p-10 rounded-3xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-2xl hover:shadow-primary/10 group feature-card tilt-card spotlight-card">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mb-8 text-primary group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Sinyal Realtime</h3>
                    <p class="text-gray-400 leading-relaxed">Eksekusi cepat di channel khusus.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass p-10 rounded-3xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-2xl hover:shadow-primary/10 group feature-card tilt-card spotlight-card">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mb-8 text-primary group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Webinar Rutin</h3>
                    <p class="text-gray-400 leading-relaxed">Materi terstruktur dan rekaman.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass p-10 rounded-3xl border border-white/5 hover:border-primary/50 transition-all duration-500 hover:shadow-2xl hover:shadow-primary/10 group feature-card tilt-card spotlight-card">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mb-8 text-primary group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition-colors">Analisis Multi‑Market</h3>
                    <p class="text-gray-400 leading-relaxed">Forex, Saham, dan Crypto.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-32 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] bg-primary/5 rounded-full blur-3xl pointer-events-none parallax-bg"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20 section-title">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Harga <span class="text-primary">Simpel & Transparan</span></h2>
                <p class="text-gray-400 text-lg">Pilih paket yang sesuai dengan perjalanan trading Anda.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto items-center">
                @foreach($packages as $package)
                <div class="glass p-8 rounded-3xl border border-white/10 hover:border-primary/30 transition-all duration-500 relative transform {{ $loop->iteration === 2 ? 'scale-105 z-10 border-2 border-primary shadow-2xl shadow-primary/20' : '' }} feature-card tilt-card spotlight-card">
                    @if($loop->iteration === 2)
                    <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-primary to-secondary text-white text-sm font-bold px-6 py-2 rounded-full shadow-lg">PALING LARIS</div>
                    @endif
                    
                    <h3 class="text-xl font-bold mb-2 {{ $loop->iteration === 2 ? 'text-white' : 'text-gray-300' }}">{{ $package->name }}</h3>
                    <div class="text-4xl font-bold mb-6 text-white">
                        Rp {{ number_format($package->price, 0, ',', '.') }}
                        <span class="text-sm text-gray-400 font-normal">
                            @if($package->is_lifetime)
                                /lifetime
                            @else
                                /{{ $package->duration_in_days }} hari
                            @endif
                        </span>
                    </div>
                    
                    @if($loop->iteration === 2)
                    <p class="text-sm text-primary mb-6 font-medium">Hemat 11% dibanding bulanan</p>
                    @endif

                    <ul class="space-y-4 mb-8 {{ $loop->iteration === 2 ? 'text-gray-300' : 'text-gray-400' }}">
                        @if($package->features)
                            @foreach($package->features as $feature)
                            <li class="flex items-center"><svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feature }}</li>
                            @endforeach
                        @endif
                    </ul>
                    
                    <a href="{{ route('checkout', $package->slug) }}" class="magnetic-btn block w-full py-4 rounded-xl {{ $loop->iteration === 2 ? 'bg-gradient-to-r from-primary to-secondary text-white hover:shadow-lg hover:shadow-primary/50' : 'border border-primary text-primary hover:bg-primary hover:text-white' }} text-center font-semibold transition-all duration-300">
                        {{ $loop->iteration === 2 ? 'Mulai Sekarang' : 'Pilih Paket' }}
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
