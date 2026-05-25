@props([
    'type' => 'button',
    'style' => 'primary',
])

@php
    switch ($style) {
        case 'link':
            $styleClass = 'text-sm text-neutral-500 dark:text-neutral-400 hover:text-black hover:dark:text-white';
            break;
        case 'icon':
            $styleClass =
                'p-2 text-sm font-medium rounded-md text-neutral-500 hover:text-black dark:text-neutral-400 dark:hover:text-white';
            break;
        case 'icon-outline':
            $styleClass =
                'p-2 text-sm font-medium text-black border rounded-md border-neutral-300 dark:text-white hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'icon-primary':
            $styleClass =
                'p-2 text-sm font-medium text-white bg-black border border-black rounded-md dark:text-black hover:bg-neutral-800 dark:bg-neutral-100 dark:hover:bg-white hover:border-neutral-800 dark:border-neutral-100 dark:hover:border-white disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'ghost':
            $styleClass =
                'px-4 py-2 text-sm font-medium text-black rounded-md dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 border border-transparent disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'outline':
            $styleClass =
                'px-4 py-2 text-sm font-medium text-black border rounded-md border-neutral-300 dark:text-white hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'danger':
            $styleClass =
                'px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-500 hover:border-red-500 disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'secondary':
            $styleClass =
                'px-4 py-2 text-sm font-medium text-black border rounded-md bg-neutral-100 hover:bg-neutral-200 border-neutral-100 dark:bg-neutral-700 dark:text-white dark:border-neutral-700 hover:border-neutral-200 dark:hover:bg-neutral-600 dark:hover:border-neutral-600 disabled:pointer-events-none disabled:opacity-60';
            break;
        case 'primary':
        default:
            $styleClass =
                'px-4 py-2 text-sm font-medium text-white bg-black border border-black rounded-md dark:text-black hover:bg-neutral-800 dark:bg-neutral-100 dark:hover:bg-white hover:border-neutral-800 dark:border-neutral-100 dark:hover:border-white disabled:pointer-events-none disabled:opacity-60';
            break;
    }
@endphp

<button type="{{ $type }}" {{ $attributes->class([$styleClass])->merge() }}>
    {{ $slot }}
</button>
