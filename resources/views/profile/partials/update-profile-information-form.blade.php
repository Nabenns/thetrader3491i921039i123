<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="space-y-6">
            <!-- Avatar Upload -->
            <div>
                <x-input-label for="avatar" :value="__('Profile Photo')" />
                <div class="flex items-center gap-6 mt-2" x-data="{ photoName: null, photoPreview: null }">
                    <!-- Current Profile Photo -->
                    <div class="mt-2" x-show="!photoPreview">
                        @if($user->avatar_url)
                            <img src="{{ Storage::url($user->avatar_url) }}" alt="{{ $user->name }}"
                                class="w-20 h-20 rounded-full object-cover border-2 border-white/10 shadow-lg">
                        @else
                            <div
                                class="w-20 h-20 rounded-full bg-primary/20 flex items-center justify-center border-2 border-dashed border-white/10 text-primary text-xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- New Profile Photo Preview -->
                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                        <span
                            class="block w-20 h-20 rounded-full bg-cover bg-no-repeat bg-center border-2 border-white/10 shadow-lg"
                            :style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input type="file" id="avatar" name="avatar" class="hidden" x-ref="photo" x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                                " />

                        <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                            {{ __('Select A New Photo') }}
                        </x-secondary-button>
                        <p class="text-xs text-gray-500">JPG, PNG, max 1MB</p>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <!-- Name Input -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Bio Input -->
            <div>
                <x-input-label for="bio" :value="__('Trading Mantra / Bio')" />
                <textarea id="bio" name="bio" rows="3"
                    class="mt-1 block w-full border-gray-700 bg-gray-900 text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                    placeholder="Write your trading philosophy or motto here...">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                <p class="text-xs text-gray-500 mt-1">This will be displayed on your dashboard.</p>
            </div>

            <!-- Email Input -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email"
                    class="mt-1 block w-full bg-gray-800 text-gray-500 cursor-not-allowed" :value="old('email', $user->email)" required autocomplete="username" disabled />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification"
                                class="underline text-sm text-gray-400 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>