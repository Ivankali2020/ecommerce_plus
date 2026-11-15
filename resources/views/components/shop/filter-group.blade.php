{{--
    Collapsible group inside a filter sidebar.
--}}
@props([
    'title',
    'open' => true,
])

<div x-data="{ open: @js($open) }" {{ $attributes->class('border-t border-line py-5') }}>
    <button type="button" @click="open = ! open" :aria-expanded="open"
        class="flex w-full items-center justify-between text-[11px] font-medium uppercase tracking-[0.2em] text-ink">
        {{ $title }}
        <x-heroicon-m-chevron-down class="size-4 text-muted transition-transform duration-300" ::class="open && 'rotate-180'" />
    </button>

    <div x-show="open" x-collapse>
        <div class="pt-4">{{ $slot }}</div>
    </div>
</div>
