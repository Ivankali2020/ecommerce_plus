@props([
    'rating' => 0,
    'size' => 'size-4',
])

<div {{ $attributes->class('flex items-center gap-0.5') }} aria-label="{{ number_format((float) $rating, 1) }} / 5">
    @for ($star = 1; $star <= 5; $star++)
        <x-heroicon-s-star @class([$size, $star <= round((float) $rating) ? 'text-accent' : 'text-line']) />
    @endfor
</div>
