<li>
    <a
        {{ $attributes->class(['text-sm font-medium inline-block w-full px-2 py-1.5 text-left rounded-md group text-black dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800'])->merge() }}>
        {{ $slot }}
    </a>
</li>
