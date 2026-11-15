@props(['align' => 'right'])

@php
    $locales = [
        'en' => ['label' => 'English', 'short' => 'EN', 'flag' => asset('assets/us.png')],
        'my' => ['label' => 'မြန်မာ', 'short' => 'မြန်မာ', 'flag' => asset('assets/mm.png')],
    ];
    $current = $locales[app()->getLocale()] ?? $locales['en'];
@endphp

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false" {{ $attributes->class('relative') }}>
    <button type="button" @click="open = ! open" :aria-expanded="open"
        class="flex h-10 items-center gap-2 rounded-full px-3 text-[13px] text-ink-soft transition-colors hover:bg-ink/5 hover:text-ink">
        <img src="{{ $current['flag'] }}" alt="" width="18" height="18" class="size-[18px] rounded-full object-cover">
        <span>{{ $current['short'] }}</span>
        <x-heroicon-m-chevron-down class="size-3.5 transition-transform" ::class="open && 'rotate-180'" />
    </button>

    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @class([
            'absolute z-50 mt-2 w-44 rounded-xl border border-line bg-surface p-1.5 shadow-xl shadow-ink/5',
            'right-0' => $align === 'right',
            'left-0 bottom-full mb-2' => $align === 'up',
        ])>
        @foreach ($locales as $code => $locale)
            <a href="{{ route('language.switch', $code) }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-ink-soft transition-colors hover:bg-canvas hover:text-ink">
                <img src="{{ $locale['flag'] }}" alt="" width="18" height="18" class="size-[18px] rounded-full object-cover">
                <span>{{ $locale['label'] }}</span>
                @if (app()->getLocale() === $code)
                    <x-heroicon-m-check class="ml-auto size-4 text-accent" />
                @endif
            </a>
        @endforeach
    </div>
</div>
