@props(['tone' => 'light'])

@php
    $toneClasses = match ($tone) {
        'sale' => 'bg-sale text-white',
        'dark' => 'bg-ink/80 text-canvas backdrop-blur-sm',
        'accent' => 'bg-accent-soft text-accent-dark',
        default => 'bg-surface/90 text-ink backdrop-blur-sm',
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-medium uppercase tracking-[0.12em] md:text-[11px]',
    $toneClasses,
]) }}>{{ $slot }}</span>
