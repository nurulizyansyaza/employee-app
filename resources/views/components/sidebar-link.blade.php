@props([
    'active' => false,
    'styleClass' =>
        'text-black rounded-md dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 border border-transparent',
])

@php
    if ($active) {
        $styleClass =
            'text-white bg-black border border-black rounded-md dark:text-black hover:bg-neutral-800 dark:bg-neutral-100 dark:hover:bg-white hover:border-neutral-800 dark:border-neutral-100 dark:hover:border-white';
    }
@endphp

<a
    {{ $attributes->class(["w-full block p-2 text-sm font-medium  $styleClass disabled:pointer-events-none disabled:opacity-60"])->merge() }}>
    {{ $slot }}
</a>
