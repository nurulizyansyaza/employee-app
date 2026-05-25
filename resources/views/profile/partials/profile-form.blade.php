<x-card class="mt-8">
    <div class="max-w-xl">
        <div class="space-y-8">
            <div class="space-y-1">
                <x-subtitle>
                    {{ __('Profile Information') }}
                </x-subtitle>
                <x-text> {{ __("Update your account's profile information and email address.") }} </x-text>
            </div>
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="space-y-4">
                    <div class="space-y-2">
                        <x-label for="name">{{ __('Name') }}</x-label>
                        <x-input type="name" id="name" name="name"
                            value="{{ old('name', auth()->user()->name) }}" placeholder="Enter name" required
                            autofocus></x-input>
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div class="space-y-2">
                        <x-label for="email">{{ __('Email') }}</x-label>
                        <x-input type="email" id="email" name="email"
                            value="{{ old('email', auth()->user()->email) }}" placeholder="Enter email"
                            required></x-input>
                        <x-input-error :messages="$errors->get('email')" />
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-8">
                    <x-button type="submit">{{ __('Save') }}</x-button>

                    @if (session('status') === 'profile-updated')
                        <x-text x-data="{ show: true }" x-show="show" x-transition
                            x-init="setTimeout(() => show = false, 2000)">{{ __('Saved.') }}</x-text>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-card>
