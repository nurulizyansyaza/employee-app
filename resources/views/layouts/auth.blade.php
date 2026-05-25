<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Metadata -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.metadata')

    <!-- Scripts -->
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

<body class="relative min-h-screen antialiased bg-whiter dark:bg-neutral-950">
    <main>
        <div class="max-w-lg px-4 py-16 mx-auto sm:py-24 lg:py-32 sm:px-6 lg:px-8">
            <div class="py-8 sm:py-12 lg:py-16">
                <x-card>
                    {{ $slot }}
                </x-card>
            </div>
        </div>
    </main>
    @include('layouts.partials.footer')
</body>

</html>
