<div x-data="{
    darkMode: localStorage.getItem('dark') === 'true',
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('dark', this.darkMode);
        this.updateTheme();
    },
    updateTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" x-init="updateTheme()">
    <button
        @click="toggleDarkMode()"
        type="button"
        class="fixed bottom-5 right-5 p-3 rounded-full bg-white/80 dark:bg-slate-800/80 shadow-lg hover:shadow-xl backdrop-blur-sm transition-all duration-300 z-50 group"
        :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <!-- Sun icon -->
        <svg
            x-show="darkMode"
            class="w-6 h-6 text-amber-500 transition-transform duration-500"
            x-transition:enter="transition-transform"
            x-transition:enter-start="rotate-180 opacity-0"
            x-transition:enter-end="rotate-0 opacity-100"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <!-- Moon icon -->
        <svg
            x-show="!darkMode"
            class="w-6 h-6 text-slate-800 transition-transform duration-500"
            x-transition:enter="transition-transform"
            x-transition:enter-start="rotate-180 opacity-0"
            x-transition:enter-end="rotate-0 opacity-100"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>

        <span class="sr-only" x-text="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"></span>
    </button>
</div>
