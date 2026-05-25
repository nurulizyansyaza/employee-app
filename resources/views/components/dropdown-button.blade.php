@props(['type' => 'button'])

<li>
    <button type="{{ $type }}"
        {{ $attributes->class(['text-sm font-medium w-full px-2 py-1.5 text-left rounded-md group hover:bg-neutral-100 dark:hover:bg-neutral-800 text-black dark:text-white'])->merge() }}>
        {{ $slot }}
    </button>
</li>
