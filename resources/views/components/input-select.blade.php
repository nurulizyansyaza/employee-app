<div class="relative z-10">
    <select
        class="relative z-20 w-full py-2 pl-2 pr-10 text-sm bg-transparent border rounded-md appearance-none text-neutral-800 dark:text-neutral-200 border-neutral-400 dark:border-neutral-600 disabled:opacity-60 disabled:pointer-events-none peer"
        {{ $attributes->merge() }}>
        {{ $slot }}
    </select>
    <svg xmlns="http://www.w3.org/2000/svg"
        class="absolute z-10 w-5 h-5 top-2 right-2 stroke-neutral-800 dark:stroke-neutral-200 peer-disabled:opacity-60"
        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
        stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M6 9l6 6l6 -6" />
    </svg>
</div>
