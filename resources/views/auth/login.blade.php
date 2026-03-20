<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <div class="animated-border">
        <div class="animated-border-content card card-pad">
            <h1 class="h1 mb-1" style="color: var(--text);">Welcome back!</h1>
            <p class="muted mb-6">Log in to continue 🩷</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" class="input mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" name="password" type="password" required autocomplete="current-password" class="input mt-1" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded" style="color: var(--brownPage-favorite-star); border-color: rgba(0,0,0,0.18);" />
                    <label for="remember_me" class="text-sm" style="color: var(--text);">{{ __('Remember me') }}</label>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-6">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm underline" style="color: var(--secondary);">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="btn-primary">
                        {{ __('Log in') }}
                    </button>
                </div>

                @if (Route::has('register'))
                    <p class="text-sm text-center mt-4" style="color: var(--muted);">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="underline" style="color: var(--secondary);">
                            {{ __('Register') }}
                        </a>
                    </p>
                @endif

            </form>
        </div>
    </div>
</x-guest-layout>