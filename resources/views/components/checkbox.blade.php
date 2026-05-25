@props([
    'id' => null,
    'placeholder' => 'Enter text',
])

<x-label for="{{ $id }}">
    <input id="{{ $id }}" type="checkbox" {{ $attributes->merge() }}
        class="accent-neutral-800 dark:accent-neutral-200 disabled:opacity-60 disabled:pointer-events-none" />
    <span>{{ $slot }}</span>
</x-label>
