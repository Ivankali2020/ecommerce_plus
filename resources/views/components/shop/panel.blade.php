{{--
    White card used for checkout steps, summaries and filter sidebars.
--}}
@props([
    'title' => null,
    'step' => null,
])

<section {{ $attributes->class('rounded-2xl border border-line bg-surface p-5 md:p-7') }}>
    @if ($title)
        <header class="mb-5 flex items-center gap-3">
            @if ($step)
                <span class="flex size-7 items-center justify-center rounded-full border border-accent text-xs font-medium text-accent">{{ $step }}</span>
            @endif
            <h2 class="font-display text-xl text-ink md:text-2xl">{{ $title }}</h2>
        </header>
    @endif

    {{ $slot }}
</section>
