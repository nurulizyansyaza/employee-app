<x-card class="mt-8">
    <div class="max-w-x">
        <div class="space-y-8">
            <div class="space-y-1">
                <x-subtitle>
                    {{ __('Update Password') }}
                </x-subtitle>
                <x-text> {{ __('Ensure your account is using a long, random password to stay secure.') }} </x-text>
            </div>
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="space-y-4">
                    <div class="space-y-2">
                        <x-label for="current-password">{{ __('Current password') }}</x-label>
                        <x-input type="password" id="current-password" name="current_password"
                            placeholder="Enter current password" required autofocus
                            autocomplete="current-password"></x-input>
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" />
                    </div>
                    <div class="space-y-2">
                        <x-label for="new-password">{{ __('New password') }}</x-label>
                        <x-input type="password" id="new-password" name="password" placeholder="Enter new password"
                            required autofocus autocomplete="new-password"></x-input>
                        <x-input-error :messages="$errors->updatePassword->get('password')" />
                    </div>
                    <div class="space-y-2">
                        <x-label for="confirm-password">{{ __('Confirm new password') }}</x-label>
                        <x-input type="password" id="confirm-password" name="password_confirmation"
                            placeholder="Enter current password" required autofocus
                            autocomplete="current-password"></x-input>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-8">
                    <x-button type="submit">{{ __('Save') }}</x-button>

                    @if (session('status') === 'password-updated')
                        <x-text x-data="{ show: true }" x-show="show" x-transition
                            x-init="setTimeout(() => show = false, 2000)">{{ __('Saved.') }}</x-text>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-card>
