{{--
    Row inside the account dropdown / mobile menu. Renders a <button> when no `href` is given.
--}}
@props([
    'href' => null,
    'icon' => null,
    'danger' => false,
])

@php
    $classes = implode(' ', [
        'flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-start text-sm transition-colors duration-150',
        $danger ? 'text-sale hover:bg-sale/5' : 'text-ink-soft hover:bg-canvas hover:text-ink',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="size-5 shrink-0" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="size-5 shrink-0" />
        @endif
        {{ $slot }}
    </button>
@endif
