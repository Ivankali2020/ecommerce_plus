{{--
    Pill-shaped filter toggle (categories, brands, sort options).
--}}
@props([
    'active' => false,
    'count' => null,
])

<button type="button" @if ($active) data-active @endif {{ $attributes->class([
    'inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm transition-colors duration-200',
    'border-ink bg-ink text-canvas' => $active,
    'border-line bg-surface text-ink-soft hover:border-ink/40 hover:text-ink' => ! $active,
]) }}>
    {{ $slot }}

    @if ($count !== null)
        <span @class([
            'text-xs tabular-nums',
            'text-canvas/60' => $active,
            'text-muted' => ! $active,
        ])>{{ $count }}</span>
    @endif
</button>
