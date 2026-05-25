@php
    $title = 'Register';
@endphp

<x-layouts.auth>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <x-title>{{ $title }}
    </x-title>

    <form method="POST" action="{{ route('register') }}" class="mt-8">
        @csrf
        <div class="space-y-4">
            <div class="space-y-2">
                <x-label for="name">{{ __('Name') }}</x-label>
                <x-input id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter name') }}"
                    required autofocus />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div class="space-y-2">
                <x-label for="email">{{ __('Email') }}</x-label>
                <x-input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ __('Enter email') }}" required />
                <x-input-error :messages="$errors->get('email')" />
            </div>
            <div class="space-y-2">
                <x-label for="password">{{ __('Password') }}</x-label>
                <x-input type="password" id="password" name="password" placeholder="{{ __('Enter password') }}"
                    required />
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="space-y-2">
                <x-label for="password-confirmation">{{ __('Password Confirmation') }}</x-label>
                <x-input type="password" id="password-confirmation" name="password_confirmation"
                    placeholder="{{ __('Confirm password') }}" required />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
        </div>
        <div class="flex items-center justify-end gap-4 mt-8">
            @if (Route::has('login'))
                <x-link href="{{ route('login') }}">{{ __('Existing user?') }}</x-link>
            @endif

            <x-button type="submit">{{ __('Register') }}</x-button>
        </div>
    </form>
</x-layouts.auth>
