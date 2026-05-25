@php
    $title = 'Email Verification';
@endphp

<x-layouts.auth>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <x-title>{{ $title }}
    </x-title>

    <div class="mt-8 space-y-2">
        <x-text>
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </x-text>

        @if (session('status') == 'verification-link-sent')
            <x-session-status :status="__(
                'A new verification link has been sent to the email address you provided during registration.',
            )" />
        @endif

    </div>
    <div class="flex items-center justify-end gap-4 mt-8">
        @if (Route::has('profile.edit'))
            <x-link href="{{ route('profile.edit') }}">{{ __('Update profile') }}</x-link>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-button type="submit">
                {{ __('Resend Verification Email') }}
            </x-button>
        </form>
    </div>
</x-layouts.auth>
