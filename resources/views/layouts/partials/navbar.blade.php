<header>
    <div class="flex items-center justify-between w-full max-w-screen-xl px-4 py-4 mx-auto sm:px-6 lg:px-8">
        <!-- Logo -->
        <x-link href="{{ route('web.index') }}" class="flex items-center gap-2">
            <span class="text-base font-semibold text-black dark:text-white">{{ config('app.name', 'Employee App') }}</span>
        </x-link>
        <!-- Dark mode toggle dropdown -->
        @include('layouts.partials.dark-mode-toggle')
    </div>
</header>
