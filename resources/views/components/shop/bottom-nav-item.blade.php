{{--
    Tab in the fixed mobile bottom bar. The default slot can hold an overlay such as a count badge.
--}}
@props([
    'href',
    'icon',
    'label',
    'active' => false,
])

<a href="{{ $href }}" wire:navigate {{ $attributes->class([
    'relative flex flex-1 flex-col items-center justify-center gap-1 text-[10px] tracking-wide transition-colors',
    'text-ink' => $active,
    'text-muted hover:text-ink' => ! $active,
]) }}>
    <x-dynamic-component :component="$icon" class="size-5" />
    <span class="max-w-full truncate px-1">{{ $label }}</span>
    @if ($active)
        <span class="absolute top-0 h-px w-8 bg-accent"></span>
    @endif
    {{ $slot }}
</a>
