<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6" 
          x-data="{ 
              password: '', 
              password_confirm: '', 
              showPassword: false,
              strength: 0,
              checks: {
                  length: false,
                  number: false,
                  symbol: false,
                  upper: false
              },
              checkPassword() {
                  this.checks.length = this.password.length >= 8;
                  this.checks.number = /[0-9]/.test(this.password);
                  this.checks.symbol = /[^A-Za-z0-9]/.test(this.password);
                  this.checks.upper = /[A-Z]/.test(this.password);
                  
                  let score = 0;
                  if(this.checks.length) score++;
                  if(this.checks.number) score++;
                  if(this.checks.symbol) score++;
                  if(this.checks.upper) score++;
                  this.strength = score;
              }
          }">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-medium text-sm text-gray-300 mb-2">Nama Lengkap</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-primary transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="name" class="block w-full pl-12 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-primary focus:ring-primary text-white placeholder-gray-500 transition-all duration-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-300 mb-2">Email</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-primary transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email" class="block w-full pl-12 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-primary focus:ring-primary text-white placeholder-gray-500 transition-all duration-300" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-medium text-sm text-gray-300 mb-2">Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-primary transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input 
                    id="password" 
                    class="block w-full pl-12 pr-12 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-primary focus:ring-primary text-white placeholder-gray-500 transition-all duration-300"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    x-model="password"
                    @input="checkPassword()"
                    required autocomplete="new-password"
                    placeholder="••••••••" 
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors focus:outline-none">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.378 5.378A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.574 2.59M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>
            </div>
            
            <!-- Password Strength Meter -->
            <div class="mt-3 space-y-2" x-show="password.length > 0" x-transition>
                <div class="flex gap-1 h-1.5">
                    <div class="h-full rounded-full flex-1 transition-colors duration-300" :class="strength >= 1 ? (strength >= 3 ? 'bg-green-500' : (strength == 2 ? 'bg-yellow-500' : 'bg-red-500')) : 'bg-white/10'"></div>
                    <div class="h-full rounded-full flex-1 transition-colors duration-300" :class="strength >= 2 ? (strength >= 3 ? 'bg-green-500' : 'bg-yellow-500') : 'bg-white/10'"></div>
                    <div class="h-full rounded-full flex-1 transition-colors duration-300" :class="strength >= 3 ? 'bg-green-500' : 'bg-white/10'"></div>
                    <div class="h-full rounded-full flex-1 transition-colors duration-300" :class="strength >= 4 ? 'bg-primary' : 'bg-white/10'"></div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-400">
                    <div class="flex items-center gap-1" :class="checks.length ? 'text-green-400' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Min. 8 karakter
                    </div>
                    <div class="flex items-center gap-1" :class="checks.upper ? 'text-green-400' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Huruf besar
                    </div>
                    <div class="flex items-center gap-1" :class="checks.number ? 'text-green-400' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Angka
                    </div>
                    <div class="flex items-center gap-1" :class="checks.symbol ? 'text-green-400' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simbol
                    </div>
                </div>
            </div>
            
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block font-medium text-sm text-gray-300 mb-2">Konfirmasi Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-primary transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <input 
                    id="password_confirmation" 
                    class="block w-full pl-12 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-primary focus:ring-primary text-white placeholder-gray-500 transition-all duration-300"
                    :type="showPassword ? 'text' : 'password'"
                    name="password_confirmation" 
                    x-model="password_confirm"
                    required autocomplete="new-password"
                    placeholder="••••••••" 
                />
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none" x-show="password.length > 0 && password === password_confirm" x-transition>
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        


        <div>
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:shadow-primary/40 transform hover:-translate-y-0.5 transition-all duration-200">
                Daftar Sekarang
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-primary hover:text-secondary font-medium transition">
                    Masuk disini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
