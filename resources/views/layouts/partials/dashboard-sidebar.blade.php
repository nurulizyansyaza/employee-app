@php
    $links = [
        [
            'title' => __('Employees'),
            'group' => 'employees.*',
            'route' => 'employees.index',
        ],
    ];
@endphp

<nav class="fixed top-0 left-0 z-20 invisible hidden w-screen h-screen lg:z-10 lg:-mt-16 lg:pt-16 lg:sticky lg:visible lg:block lg:w-64 lg:bg-transparent bg-neutral-500/50 lg:shrink-0"
    x-data="{
        isSidebarMenuOpen: false,
        breakpointSize: 1024,
        isClickOutside(event) {
            if (!event.target.closest(`#sidebar-menu-content`)) {
                this.isSidebarMenuOpen = false;
            }
        },
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => !el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }" x-init="$watch('isSidebarMenuOpen', isSidebarMenuOpen => {
        if (isSidebarMenuOpen) {
            $el.setAttribute('tabindex', 1);
            $el.focus();
            setTimeout(() => {
                $el.removeAttribute('tabindex');
            }, 100);
        }
    })" @open-sidebar-menu.window="isSidebarMenuOpen = true"
    @resize.window="if(window.innerWidth >= breakpointSize) isSidebarMenuOpen = false"
    @keydown.escape.window="isSidebarMenuOpen = false" @keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    @keydown.shift.tab.prevent="prevFocusable().focus()" :class="{ 'hidden invisible': !isSidebarMenuOpen }"
    @click="if(window.innerWidth < breakpointSize) isClickOutside(event)">

    <div id="sidebar-menu-content"
        class="fixed top-0 left-0 h-full px-4 overflow-y-auto bg-white border-r shadow sm:px-6 lg:px-8 lg:border-0 border-neutral-200 dark:border-neutral-800 lg:sticky w-72 lg:w-64 lg:bg-transparent lg:dark:bg-transparent lg:shadow-none dark:bg-neutral-800">

        <x-link href="{{ route('employees.index') }}" class="inline-block mt-4 sm:mt-6 lg:hidden">
            <span class="text-lg font-bold text-black dark:text-white">{{ config('app.name', 'Employee App') }}</span>
        </x-link>

        <x-button style="icon" class="absolute right-2 sm:right-4 top-2 sm:top-4 group lg:hidden"
            aria-label="Close sidebar menu." @click="isSidebarMenuOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 stroke-neutral-500 dark:stroke-neutral-400 group-hover:stroke-black dark:group-hover:stroke-white"
                width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
            </svg>
        </x-button>

        <ul class="mt-6 space-y-1 lg:pt-10">
            @foreach ($links as $index => $link)
                <li>
                    @if (isset($link['route']))
                        @if (isset($link['group']))
                            <x-sidebar-link href="{{ route($link['route']) }}"
                                :active="request()->routeIs($link['route']) || request()->routeIs($link['group'])">{{ $link['title'] }}</x-sidebar-link>
                        @else
                            <x-sidebar-link href="{{ route($link['route']) }}"
                                :active="request()->routeIs($link['route'])">{{ $link['title'] }}</x-sidebar-link>
                        @endif
                    @else
                        <p class='px-2 pt-6 text-base font-semibold uppercase text-neutral-400'>
                            {{ $link['title'] }}</p>
                    @endif
                </li>
            @endforeach

            <li>
                <button type="button"
                    @click="window.dispatchEvent(new CustomEvent('open-ocr-scanner'))"
                    class="block w-full p-2 text-sm font-medium text-left text-black border border-transparent rounded-md dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800">
                    {{ __('Scan Plate (OCR)') }}
                </button>
            </li>
        </ul>
    </div>
</nav>
