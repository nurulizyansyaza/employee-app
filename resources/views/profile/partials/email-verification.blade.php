<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf

    <x-card class="space-y-2">
        <div class="flex items-center gap-2">
            <x-text>
                {{ __('Your email address is unverified.') }}
            </x-text>

            <x-button form="send-verification" type="submit" style="link" class="underline">
                {{ __('Click here to re-send the verification email.') }}
            </x-button>
        </div>
        @if (session('status') === 'verification-link-sent')
            <x-session-status :status="__('A new verification link has been sent to your email address.')"></x-session-status>
        @endif
    </x-card>
</form>
