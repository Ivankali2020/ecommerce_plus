<div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
        @if(app()->getLocale() === 'en')
            <img src="{{  asset('assets/us.png') }}"
                 width="20"
                 height="20"
                 class="rounded-xs object-contain"
                 alt="English">
            <span class="font-jost">English</span>
        @else
            <img src="{{  asset('assets/mm.png') }}"
                 width="20"
                 height="20"
                 class="rounded-xs object-contain"
                 alt="Myanmar">
            <span class="font-mm">မြန်မာ</span>
        @endif
        {{-- <svg class="w-4 h-4 transition-transform duration-200"
             :class="{ 'rotate-180': open }"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg> --}}
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-48 z-50 relative rounded-md shadow-lg bg-white dark:bg-gray-800 ring-0  ring-black ring-opacity-5">
        <div class="py-1 font-jost ">
            <a href="{{ route('language.switch', 'en') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <img src="{{  asset('assets/us.png') }}"
                     width="20"
                     height="20"
                     class="rounded-xs object-contain"
                     alt="English">
                <span class="font-medium">English</span>
                @if(app()->getLocale() === 'en')
                    <svg class="w-4 h-4 ml-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </a>
            <a href="{{ route('language.switch', 'my') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <img  src="{{  asset('assets/mm.png') }}"
                     width="20"
                     height="20"
                     class="rounded-xs object-contain"
                     alt="Myanmar">
                <span class="font-medium font-mm ">မြန်မာ</span>
                @if(app()->getLocale() === 'my')
                    <svg class="w-4 h-4 ml-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </a>
        </div>
    </div>
</div>
