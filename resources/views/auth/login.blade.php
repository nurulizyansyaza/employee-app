@php
    $title = 'Login';
@endphp

<x-layouts.auth>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <x-title>{{ $title }}</x-title>

    <form method="POST" action="{{ route('login') }}" class="mt-8">
        @csrf
        <div class="space-y-4">
            <div class="space-y-2">
                <x-label for="email">{{ __('Email') }}</x-label>
                <x-input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ __('Enter email') }}" required autofocus autocomplete="username"></x-input>
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="space-y-2">
                <x-label for="password">{{ __('Password') }}</x-label>
                <x-input type="password" id="password" name="password" value=""
                    class="w-full p-2 text-sm bg-transparent border rounded-md appearance-none text-neutral-800 dark:text-neutral-200 border-neutral-400 dark:border-neutral-600 disabled:opacity-60 disabled:pointer-events-none"
                    placeholder="{{ __('Enter password') }}" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
                <div class="flex justify-between">
                    <x-checkbox id="remember" type="checkbox" name="remember">{{ __('Remember me') }}</x-checkbox>
                    @if (Route::has('password.request'))
                        <x-link href="{{ route('password.request') }}">{{ __('Forgot password?') }}</x-link>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-4 mt-8">
            @if (Route::has('register'))
                <x-link href="{{ route('register') }}">{{ __('New user?') }}</x-link>
            @endif

            <x-button type="submit">{{ __('Login') }}</x-button>
        </div>
    </form>
</x-layouts.auth>
