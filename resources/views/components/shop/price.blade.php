{{--
    Formatted MMK price. Pass `original` to show a struck-through price next to it.
--}}
@props([
    'amount',
    'original' => null,
    'size' => 'md',
])

@php
    $hasOriginal = $original !== null && (float) $original > (float) $amount;

    $amountClasses = match ($size) {
        'sm' => 'text-sm',
        'lg' => 'text-2xl md:text-3xl',
        default => 'text-base md:text-lg',
    };
@endphp

<div {{ $attributes->class('flex flex-wrap items-baseline gap-x-2 gap-y-0.5') }}>
    <span @class(['font-medium tabular-nums', $hasOriginal ? 'text-sale' : 'text-ink', $amountClasses])>
        {{ number_format((float) $amount) }}
        <span class="text-[0.7em] font-normal tracking-wide text-ink-soft">{{ __('messages.mmk') }}</span>
    </span>

    @if ($hasOriginal)
        <span class="text-xs text-muted line-through tabular-nums md:text-sm">{{ number_format((float) $original) }}</span>
    @endif
</div>
