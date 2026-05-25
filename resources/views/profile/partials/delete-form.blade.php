<x-card class="mt-8">
    <div class="max-w-xl">
        <div class="space-y-8">
            <div class="space-y-1">
                <x-subtitle>
                    {{ __('Delete Account') }}
                </x-subtitle>
                <x-text>
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </x-text>
            </div>
            <x-button style="danger" x-data
                @click="$dispatch('open-modal', 'confirm-delete')">{{ __('Delete') }}</x-button>
        </div>
    </div>
</x-card>

<x-modal size="md" name="confirm-delete" :isModalOpen="$errors->userDeletion->isNotEmpty()">
    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-8">
        @csrf
        @method('delete')
        <div class="space-y-1">
            <x-subtitle>
                {{ __('Are you sure you want to delete your account?') }}
            </x-subtitle>
            <x-text>
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </x-text>
        </div>
        <div class="space-y-2">
            <x-label for="password">{{ __('Password') }}</x-label>
            <x-input type="password" id="password" name="password" value=""
                class="w-full p-2 text-sm bg-transparent border rounded-md appearance-none text-neutral-800 dark:text-neutral-200 border-neutral-400 dark:border-neutral-600 disabled:opacity-60 disabled:pointer-events-none"
                placeholder="Enter password" required autocomplete="password" />
            <x-input-error :messages="$errors->userDeletion->get('password')" />
        </div>
        <div class="flex justify-end gap-2">
            <x-button style="secondary" @click="$dispatch('close')">{{ __('Cancel') }}</x-button>
            <x-button type="submit" style="danger">{{ __('Delete') }}</x-button>
        </div>
    </form>
</x-modal>
