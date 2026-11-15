{{--
    Section title in the display serif, with an optional "view all" link on the right.
--}}
@props([
    'title',
    'eyebrow' => null,
    'href' => null,
    'linkLabel' => null,
    'as' => 'h2',
])

<div {{ $attributes->class('mb-6 flex items-end justify-between gap-4 md:mb-10') }}>
    <div>
        @if ($eyebrow)
            <p class="mb-2 text-[11px] font-medium uppercase tracking-[0.25em] text-accent">{{ $eyebrow }}</p>
        @endif

        <{{ $as }} class="font-display text-3xl font-medium leading-tight text-ink md:text-4xl">{{ $title }}</{{ $as }}>
    </div>

    @if ($href)
        <a href="{{ $href }}" wire:navigate
            class="group inline-flex shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-[0.18em] text-ink-soft transition-colors hover:text-ink">
            {{ $linkLabel ?? __('messages.view_all') }}
            <x-heroicon-m-arrow-long-right class="size-4 transition-transform group-hover:translate-x-1" />
        </a>
    @endif
</div>
