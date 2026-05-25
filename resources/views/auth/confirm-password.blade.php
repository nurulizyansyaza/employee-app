<x-layouts.auth>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-8">
        @csrf

        <x-text>
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </x-text>
        <div class="space-y-2">
            <x-label for="password">{{ __('Password') }}</x-label>
            <x-input type="password" id="password" name="password" value=""
                class="w-full p-2 text-sm bg-transparent border rounded-md appearance-none text-neutral-800 dark:text-neutral-200 border-neutral-400 dark:border-neutral-600 disabled:opacity-60 disabled:pointer-events-none"
                placeholder="{{ __('Enter password') }}" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="flex justify-end">
            <x-button type="submit">
                {{ __('Confirm') }}
            </x-button>
        </div>
    </form>
</x-layouts.auth>
