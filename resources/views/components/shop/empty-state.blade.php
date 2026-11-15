{{--
    Centered placeholder for empty lists. `icon` is a Heroicon component name, e.g. "heroicon-o-shopping-bag".
    The default slot is rendered below the text, typically for a call-to-action button.
--}}
@props([
    'icon' => 'heroicon-o-sparkles',
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center px-6 py-16 text-center md:py-24') }}>
    <div class="mb-6 flex size-16 items-center justify-center rounded-full bg-accent-soft text-accent">
        <x-dynamic-component :component="$icon" class="size-7" />
    </div>

    <h3 class="font-display text-2xl text-ink">{{ $title }}</h3>

    @if ($description)
        <p class="mt-2 max-w-sm text-sm leading-relaxed text-ink-soft">{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-8">{{ $slot }}</div>
    @endif
</div>
