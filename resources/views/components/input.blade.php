@props([
    'type' => 'text',
    'placeholder' => 'Enter text',
])

<input type="{{ $type }}"
    {{ $attributes->class(['w-full p-2 text-sm bg-transparent border rounded-md appearance-none text-neutral-800 dark:text-neutral-200 border-neutral-400 dark:border-neutral-600 disabled:opacity-60 disabled:pointer-events-none'])->merge() }}
    placeholder="{{ $placeholder }}" />
