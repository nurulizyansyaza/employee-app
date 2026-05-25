<span x-data="{
    updateTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        toggleTheme(theme = null) {
            theme != null ? localStorage.theme = theme : localStorage.removeItem('theme');
            this.updateTheme();
            this.isDropdownOpen = false;
        },
}" x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({ matches }) => {
    updateTheme();
})">
    <x-dropdown aria-label="Toggle dark mode toggle dropdown menu." size="sm" style='icon'>
        <x-slot name="trigger">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 dark:hidden stroke-neutral-500 group-hover:stroke-black" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14.828 14.828a4 4 0 1 0 -5.656 -5.656a4 4 0 0 0 5.656 5.656z"></path>
                <path d="M6.343 17.657l-1.414 1.414"></path>
                <path d="M6.343 6.343l-1.414 -1.414"></path>
                <path d="M17.657 6.343l1.414 -1.414"></path>
                <path d="M17.657 17.657l1.414 1.414"></path>
                <path d="M4 12h-2"></path>
                <path d="M12 4v-2"></path>
                <path d="M20 12h2"></path>
                <path d="M12 20v2"></path>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg"
                class="hidden w-5 h-5 dark:block stroke-neutral-400 group-hover:stroke-white" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"></path>
                <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2"></path>
                <path d="M19 11h2m-1 -1v2"></path>
            </svg>
        </x-slot>

        <x-slot name="menu">
            <x-dropdown-button class="flex items-center gap-2" @click="toggleTheme('light')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-black dark:stroke-white" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14.828 14.828a4 4 0 1 0 -5.656 -5.656a4 4 0 0 0 5.656 5.656z"></path>
                    <path d="M6.343 17.657l-1.414 1.414"></path>
                    <path d="M6.343 6.343l-1.414 -1.414"></path>
                    <path d="M17.657 6.343l1.414 -1.414"></path>
                    <path d="M17.657 17.657l1.414 1.414"></path>
                    <path d="M4 12h-2"></path>
                    <path d="M12 4v-2"></path>
                    <path d="M20 12h2"></path>
                    <path d="M12 20v2"></path>
                </svg>
                <span>Light</span>
            </x-dropdown-button>

            <x-dropdown-button class="flex items-center gap-2" @click="toggleTheme('dark')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-black dark:stroke-white" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z">
                    </path>
                    <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2"></path>
                    <path d="M19 11h2m-1 -1v2"></path>
                </svg>
                <span>Dark</span>
            </x-dropdown-button>
            <x-dropdown-button class="flex items-center gap-2" @click="toggleTheme()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-black dark:stroke-white" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z">
                    </path>
                    <path d="M7 20h10"></path>
                    <path d="M9 16v4"></path>
                    <path d="M15 16v4"></path>
                </svg>

                <span>System</span>
            </x-dropdown-button>
        </x-slot>
    </x-dropdown>
</span>
