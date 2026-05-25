<header class="sticky top-0 z-10 w-full bg-white lg:z-20 dark:bg-neutral-950" x-data="{
    navbarHeight: null,
    isScrolled: false,
}"
    x-init="navbarHeight = $el.clientHeight;
    isScrolled = window.scrollY > 0;"
    :class="isScrolled ? 'border-b border-neutral-200 dark:border-neutral-800' :
        'border-transparent dark:border-transparent'"
    @scroll.window="

        // Toggle navbar appearance
        isScrolled = window.scrollY > 0;
    ">
    <div class="flex items-center justify-between w-full h-16 max-w-screen-xl px-4 mx-auto sm:px-6 lg:px-8">
        {{-- Sidebar menu open button --}}
        <div class="flex items-center justify-between gap-2">

            @if (auth()->check() && auth()->user()->email_verified_at)
                <x-button class="-ml-2 group lg:hidden" style="icon" aria-label="Open sidebar menu."
                    @click="$dispatch('open-sidebar-menu')">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 stroke-neutral-500 dark:stroke-neutral-400 group-hover:stroke-black dark:group-hover:stroke-white"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 6l16 0" />
                        <path d="M4 12l16 0" />
                        <path d="M4 18l16 0" />
                    </svg>
                </x-button>
            @endif

            <x-link href="{{ route('employees.index') }}" class="inline-block">
                <span class="text-lg font-bold text-black dark:text-white">{{ config('app.name', 'Employee App') }}</span>
            </x-link>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1">

                {{-- Notification --}}

                {{-- @if (auth()->check() && auth()->user()->email_verified_at) --}}
                    {{-- <x-link href="#" class="block p-2 group" aria-label="Go to notification."> --}}
                        {{-- <span class="relative"> --}}
                            {{-- <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 stroke-neutral-500 dark:stroke-neutral-400 group-hover:stroke-black dark:group-hover:stroke-white"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg> --}}

                            {{-- Notification alert --}}
                            {{-- <div class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></div> --}}
                        {{-- </span> --}}
                    {{-- </x-link> --}}
                {{-- @endif --}}


                {{-- Dark mode toggle dropdown menu --}}
                @include('layouts.partials.dark-mode-toggle')
            </div>

            {{-- Menu dropdown menu --}}
            <x-dropdown aria-label="Toggle menu dropdown menu.">
                <x-slot name="trigger">{{ __('Menu') }}</x-slot>
                <x-slot name="menu">
                    {{-- @if (Route::has('profile.edit'))
                        <x-dropdown-link href="{{ route('profile.edit') }}">{{ __('Profile') }}</x-dropdown-link>
                    @endif --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-button type="submit">
                            {{ __('Log Out') }}
                        </x-dropdown-button>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
