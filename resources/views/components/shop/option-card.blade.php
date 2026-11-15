{{--
    Selectable card wrapping a radio/checkbox input (addresses, payment methods, accounts).
--}}
@props(['selected' => false])

<label {{ $attributes->class([
    'relative block cursor-pointer rounded-xl border p-4 transition-colors duration-200',
    'border-ink bg-canvas' => $selected,
    'border-line bg-surface hover:border-ink/30' => ! $selected,
]) }}>
    {{ $slot }}
</label>
