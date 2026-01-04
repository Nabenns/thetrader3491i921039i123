<div class="relative min-h-screen flex items-center justify-center overflow-hidden py-12 px-4 sm:px-6 lg:px-8" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-background-dark via-[#0F282D] to-background-dark animate-gradient-slow -z-20"></div>
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay -z-10"></div>
    
    <!-- Floating Blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-blob mix-blend-screen -z-10"></div>
    <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-blob animation-delay-2000 mix-blend-screen -z-10"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-blob animation-delay-4000 mix-blend-screen -z-10"></div>

    <div 
        class="max-w-lg w-full glass rounded-3xl overflow-hidden shadow-2xl border border-white/10 relative z-10 transition-all duration-1000 transform"
        :class="loaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
    >
        <div class="p-8 sm:p-10">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Checkout</h2>
                <p class="text-gray-400">Selesaikan pembayaran untuk mulai akses.</p>
            </div>
            
            <div class="bg-black/20 p-6 rounded-2xl border border-white/5 mb-8">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $package->name }}</h3>
                        <p class="text-sm text-gray-400">
                            @if($package->is_lifetime)
                                Akses Seumur Hidup
                            @else
                                Durasi {{ $package->duration_in_days }} Hari
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-primary">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-400">Duration</span>
                        <span class="text-white">{{ $package->duration_in_days }} Days</span>
                    </div>
                    
                    <!-- Coupon Input -->
                    <div class="mt-4 mb-4">
                        <label class="block text-xs text-gray-400 mb-1">Kode Kupon</label>
                        <div class="flex gap-2">
                            <input wire:model="couponCode" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-primary transition-colors" placeholder="Masukkan kode">
                            <button wire:click="applyCoupon" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Terapkan
                            </button>
                        </div>
                        @error('couponCode') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @if($couponMessage) <span class="text-green-400 text-xs mt-1 block">{{ $couponMessage }}</span> @endif
                    </div>

                    <div class="border-t border-white/10 my-2 pt-2">
                        <div class="flex justify-between items-center mb-1 text-sm">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="text-gray-300">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($discount > 0)
                        <div class="flex justify-between items-center mb-1 text-sm">
                            <span class="text-green-400">Diskon ({{ $couponCode }})</span>
                            <span class="text-green-400">-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/10">
                            <span class="text-gray-300 font-bold">Total</span>
                            <span class="text-primary text-xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($package->features)
                <div class="border-t border-white/10 my-4 pt-4">
                    <ul class="space-y-3">
                        @foreach($package->features as $feature)
                        <li class="flex items-center text-sm text-gray-300">
                            <svg class="w-4 h-4 text-primary mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <button 
                wire:click="pay" 
                wire:loading.attr="disabled" 
                class="w-full py-4 rounded-xl bg-gradient-to-r from-primary to-secondary text-white font-bold shadow-lg shadow-primary/25 hover:shadow-primary/50 hover:-translate-y-1 transition-all duration-300 flex justify-center items-center group relative overflow-hidden"
            >
                <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-500 -skew-x-12 -translate-x-full"></div>
                
                <span wire:loading.remove class="relative z-10">Bayar Sekarang</span>
                <span wire:loading class="flex items-center relative z-10">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
            
            <p class="text-center text-xs text-gray-500 mt-6 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Pembayaran Aman & Terenkripsi
            </p>
        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('snap-token-received', (event) => {
                window.snap.pay(event.token, {
                    onSuccess: function(result){
                        window.location.href = '/dashboard';
                    },
                    onPending: function(result){
                        window.location.href = '/dashboard';
                    },
                    onError: function(result){
                        alert("Payment failed!");
                    },
                    onClose: function(){
                        // Optional: Handle close event
                    }
                });
            });
        });
    </script>
</div>
