@php
    $title = 'Reset Password';
@endphp

<x-layouts.auth>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <x-title>{{ $title }}
    </x-title>

    <form method="POST" action="{{ route('password.store') }}"class="mt-8">
        @csrf
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-4">
            <div class="space-y-2">
                <x-label for="email">{{ __('Email') }}</x-label>
                <x-input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ __('Enter email') }}" required autofocus autocomplete="username"></x-input>
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
            <x-button type="submit">{{ __('Reset Password') }}</x-button>
        </div>
    </form>
</x-layouts.auth>
