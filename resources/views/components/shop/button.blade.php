{{--
    Storefront button. Renders an <a> when `href` is given, otherwise a <button>.

    Variants: primary (ink), accent (gold), secondary (outlined), ghost (text only).
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'block' => false,
])

@php
    $variantClasses = match ($variant) {
        'accent' => 'bg-accent text-white hover:bg-accent-dark',
        'secondary' => 'border border-ink/15 bg-surface text-ink hover:border-ink/40',
        'ghost' => 'text-ink-soft hover:text-ink',
        default => 'bg-ink text-canvas hover:bg-ink/85',
    };

    $sizeClasses = match ($size) {
        'sm' => 'h-9 px-4 text-xs',
        'lg' => 'h-12 px-8 text-sm',
        default => 'h-11 px-6 text-sm',
    };

    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-full font-medium tracking-wide',
        'transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent',
        'disabled:cursor-not-allowed disabled:opacity-40',
        $variantClasses,
        $sizeClasses,
        $block ? 'w-full' : '',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class($classes) }}>{{ $slot }}</button>
@endif
