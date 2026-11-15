@props([
    'label',
    'emphasis' => false,
])

<div {{ $attributes->class([
    'flex items-baseline justify-between gap-4',
    'text-base font-medium text-ink' => $emphasis,
    'text-sm text-ink-soft' => ! $emphasis,
]) }}>
    <span>{{ $label }}</span>
    <span class="tabular-nums {{ $emphasis ? 'font-display text-2xl' : 'text-ink' }}">{{ $slot }}</span>
</div>
