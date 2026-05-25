@php
    $title = 'Profile';
@endphp

<x-layouts.dashboard>
    <x-slot:title>
        {{ $title }}
    </x-slot>


    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
        @include('profile.partials.email-verification')
    @endif
    @include('profile.partials.profile-form')
    @include('profile.partials.password-form')
    @include('profile.partials.delete-form')

</x-layouts.dashboard>
