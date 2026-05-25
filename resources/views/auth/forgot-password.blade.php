@php
    $title = 'Forgot Password';
@endphp

<x-layouts.auth>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <x-title>{{ $title }}
    </x-title>
    {{ __('Enter password') }}
    <form method="POST" action="{{ route('password.email') }}"class="mt-8">
        @csrf
        <div class="space-y-2">
            <x-text>
                {{ __('Enter your email address below so we can send you a password reset link.') }}
            </x-text>
            <x-input type="email" id="email" name="email" value="{{ old('email') }}"
                placeholder="{{ __('Enter email') }}" />
            <x-input-error :messages="$errors->get('email')" />
            <x-session-status :status="session('status')" />
        </div>
        <div class="flex items-center justify-end gap-4 mt-8">
            @if (Route::has('login'))
                <x-link href="{{ route('login') }}">{{ __('Back') }}</x-link>
            @endif
            <x-button type="submit">{{ __('Email Reset Password Link') }}</x-button>
        </div>
    </form>
</x-layouts.auth>
