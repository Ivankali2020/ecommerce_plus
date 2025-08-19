@props(['disabled' => false, 'type' => 'text'])

<div x-data="{ show: false , type : '{{ $type }}'}" wire:ignore class="relative">
    <input
        :type="type === 'password' ? (show ? 'text' : 'password') : type"
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-slate-500 dark:focus:border-slate-600 focus:outline-none focus:ring-0 rounded-md w-full']) }}
    >
    @if ($type === 'password')
        <button type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 z-10 px-3 flex items-center text-gray-400"
            tabindex="-1"
        >
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95M6.62 6.62A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.293 5.95M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </button>
    @endif
</div>
