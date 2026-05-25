@props([
    'ariaLabel' => 'Toggle dropdown.',
    'size' => 'sm',
    'style' => 'primary',
])

@php
    switch ($size) {
        case 'xl':
            $sizeClass = 'w-72';
            break;
        case 'lg':
            $sizeClass = 'w-64';
            break;
        case 'md':
            $sizeClass = 'w-48';
            break;
        case 'sm':
        default:
            $sizeClass = 'w-36';
            break;
    }
@endphp

<div class="relative" x-data="{
    isDropdownOpen: false
}" @click.outside="isDropdownOpen=false"
    @keydown.escape.window="isDropdownOpen = false">
    <x-button class="group" style="{{ $style }}" aria-label="{{ $ariaLabel }}"
        @click="isDropdownOpen = !isDropdownOpen" ::aria-expanded="isDropdownOpen ? true : false" type="button">
        @isset($trigger)
            {{ $trigger }}
        @endisset
    </x-button>
    @isset($menu)
        <ul class="absolute right-0 z-10 invisible hidden {{ $sizeClass }} p-0.5 top-12 bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
            :class="{
                'hidden invisible': !isDropdownOpen
            }"
            :aria-hidden="isDropdownOpen ? false : true">

            {{ $menu }}
        </ul>
    @endisset
</div>
