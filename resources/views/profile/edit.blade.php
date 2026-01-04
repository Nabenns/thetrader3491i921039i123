<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">{{ __('Profile') }}</h2>
            <p class="mt-2 text-gray-400">Kelola informasi akun dan keamanan Anda.</p>
        </div>

        <div x-data="{ activeTab: 'profile' }" class="space-y-8">
            <!-- Tabs Navigation -->
            <div class="flex space-x-1 bg-white/5 p-1 rounded-xl border border-white/10 w-fit">
                <button @click="activeTab = 'profile'" :class="{ 'bg-primary text-white shadow': activeTab === 'profile', 'text-gray-400 hover:text-white': activeTab !== 'profile' }" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Profile
                </button>
                <button @click="activeTab = 'security'" :class="{ 'bg-primary text-white shadow': activeTab === 'security', 'text-gray-400 hover:text-white': activeTab !== 'security' }" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Security
                </button>
                <button @click="activeTab = 'subscription'" :class="{ 'bg-primary text-white shadow': activeTab === 'subscription', 'text-gray-400 hover:text-white': activeTab !== 'subscription' }" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Subscription
                </button>
                <button @click="activeTab = 'activity'" :class="{ 'bg-primary text-white shadow': activeTab === 'activity', 'text-gray-400 hover:text-white': activeTab !== 'activity' }" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Activity
                </button>
            </div>

            <!-- Profile Tab -->
            <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-4 sm:p-8 glass rounded-2xl border border-white/10">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                <div class="p-4 sm:p-8 glass rounded-2xl border border-white/10">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
                <div class="p-4 sm:p-8 glass rounded-2xl border border-white/10">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Subscription Tab -->
            <div x-show="activeTab === 'subscription'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-4 sm:p-8 glass rounded-2xl border border-white/10">
                <div class="max-w-xl">
                    @include('profile.partials.subscription-details')
                </div>
            </div>

            <!-- Activity Tab -->
            <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-4 sm:p-8 glass rounded-2xl border border-white/10">
                <div class="max-w-xl">
                    @include('profile.partials.activity-log')
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
