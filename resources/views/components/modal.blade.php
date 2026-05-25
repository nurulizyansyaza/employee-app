@props([
    'name' => null,
    'isModalOpen' => false,
    'size' => null,
])

@php
    switch ($size) {
        case 'sm':
            $sizeClass = 'max-w-4xl';
            break;
        case 'md':
        default:
            $sizeClass = 'max-w-screen-sm';
            break;
    }
@endphp

<div {{ $attributes->class(['invisible hidden fixed inset-0 z-50 px-4 bg-neutral-500/50 sm:px-6 lg:px-8'])->merge() }}
    x-data="{
        isModalOpen: @js($isModalOpen),
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
    }" x-init="$watch('isModalOpen', isModalOpen => {
        if (isModalOpen) {
            $el.setAttribute('tabindex', 1);
            $el.focus();
            setTimeout(() => {
                $el.removeAttribute('tabindex');
            }, 100);
        }
    })"
    @open-modal.window="$event.detail == '{{ $name }}' ? isModalOpen = true : null"
    @close-modal.window="$event.detail == '{{ $name }}' ? isModalOpen = false : null"
    @close.stop="isModalOpen = false" @keydown.escape.window="isModalOpen = false"
    @keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()" @keydown.shift.tab.prevent="prevFocusable().focus()"
    :class="{ 'hidden invisible': !isModalOpen }">

    <x-card class="relative max-w-screen-sm mx-auto mt-32" x-show="isModalOpen" @click.outside="isModalOpen = false">
        <x-button class="absolute top-2 right-2 sm:top-4 sm:right-4 lg:top-6 lg:right-6" style="icon"
            aria-label="Close modal." @click="isModalOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 stroke-neutral-500 dark:stroke-neutral-400 group-hover:stroke-black dark:group-hover:stroke-white"
                width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
            </svg>
        </x-button>
        {{ $slot }}
    </x-card>
</div>
