<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.metadata')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Below code must execute here to avoid FOUC. -->
    <script defer>
        // Handle dark mode toggle when the page is loaded.
        if (
            localStorage.theme === "dark" ||
            (!("theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }

        /**
         * Workaround fix to handle viewport height issue on mobile browsers
         * https://stackoverflow.com/questions/37112218/css3-100vh-not-constant-in-mobile-browser
         */
        const resizeViewportHeight = () => {
            document.documentElement.style.setProperty(
                "--vh",
                window.innerHeight * 0.01 + "px"
            );
        };

        window.addEventListener("resize", resizeViewportHeight);
        resizeViewportHeight();
    </script>

    <title>
        @isset($title)
            {{ "$title | " }}
        @endisset {{ config('app.name', 'Employee App') }}
    </title>
</head>

<body class="relative min-h-screen antialiased bg-white dark:bg-neutral-950">

    @include('layouts.partials.dashboard-navbar')
    <div class="flex w-full max-w-screen-xl mx-auto">


        @if (auth()->check() && auth()->user()->email_verified_at)
            @include('layouts.partials.dashboard-sidebar')
        @endif

        <div class="relative flex-auto w-full px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8">
                @isset($title)
                    <div class="mb-2">
                        <x-title>{{ $title }}</x-title>
                    </div>
                @endisset
                @isset($subtitle)
                    <div>
                        <x-subtitle>{{ $subtitle }}</x-subtitle>
                    </div>
                @endisset
            </div>

            @if (session('alert') !== null)
                <div class="relative px-4 mt-8 border rounded-md sm:px-6 lg:px-8 bg-neutral-100 dark:bg-neutral-700 border-neutral-200 dark:border-neutral-800"
                    x-data="{ isOpen: true }" x-show="isOpen">
                    <x-button class="absolute top-2 right-2 sm:right-4 lg:right-6" style="icon"
                        aria-label="Close alert." @click="isOpen = false">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 stroke-neutral-500 dark:stroke-neutral-400 group-hover:stroke-black dark:group-hover:stroke-white"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </x-button>
                    <div class="py-4 pr-2">
                        <x-text>{{ session('alert') }}</x-text>
                    </div>
                </div>
            @endisset

            <main class="mt-8">
                {{ $slot }}
            </main>
            @include('layouts.partials.footer')
    </div>
</div>
</body>

</html>
