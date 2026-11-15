{{--
    Wholesale tile for either a Product or a Variant.
    The host Livewire component must use the WithCart trait (`addToCart` / `addVariantToCart`).
--}}
@props([
    'item',
    'kind' => 'product',
])

@php
    $isVariant = $kind === 'variant';
    $product = $isVariant ? $item->product : $item;

    $image = $isVariant
        ? ($item->getFirstMediaUrl('images', 'thumb') ?: $product->productImgThumb)
        : $item->productImgThumb;
    $image = $image ?: ($tenantSellerLogo ?? '');

    $hasDiscount = $item->discount > 0;
    $currentPrice = $hasDiscount ? $item->mmk_discount_price : $item->mmk_price;
    $isOutOfStock = $item->quantity <= 0;
    $moq = $item->MOQ ?? $product->MOQ;

    $cartAction = $isVariant
        ? "addVariantToCart({$item->id}, {$moq})"
        : "addToCart({$item->id}, null, {$moq})";
@endphp

<article wire:key="wholesale-{{ $kind }}-{{ $item->id }}" {{ $attributes->class('group flex flex-col animate-fade-in-up') }}>
    <a href="{{ route('product', $product->id) }}" wire:navigate
        class="relative block aspect-[4/5] overflow-hidden rounded-2xl bg-accent-soft/50">
        <img src="{{ $image }}" alt="{{ $item->name }}" loading="lazy"
            class="size-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]" />

        <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
            @if ($hasDiscount)
                <x-shop.badge tone="sale">−{{ $item->discount }}%</x-shop.badge>
            @else
                <span></span>
            @endif

            <x-shop.badge :tone="$isVariant ? 'accent' : 'light'">
                {{ $isVariant ? __('messages.wholesale.variant') : __('messages.wholesale.wholesale') }}
            </x-shop.badge>
        </div>

        @if ($isOutOfStock)
            <div class="absolute inset-0 flex items-center justify-center bg-canvas/60 backdrop-blur-[2px]">
                <x-shop.badge tone="dark">{{ __('messages.out_of_stock') }}</x-shop.badge>
            </div>
        @endif
    </a>

    <div class="flex grow flex-col gap-1.5 pt-4">
        <p class="truncate text-[10px] uppercase tracking-[0.2em] text-muted md:text-[11px]">
            {{ $product->category?->name }}
            @if ($product->brand)
                <span class="mx-1">·</span>{{ $product->brand->name }}
            @endif
        </p>

        <h3 class="line-clamp-2 text-sm leading-snug text-ink md:text-base">{{ $item->name }}</h3>

        @if ($isVariant)
            <p class="truncate text-xs text-ink-soft">{{ $product->name }}</p>
            <x-shop.attribute-chips :items="$item->attributeValues ?? []" :with-labels="false" class="pt-1" />
        @endif

        <x-shop.price :amount="$currentPrice" :original="$hasDiscount ? $item->mmk_price : null" size="sm" class="mt-auto pt-1" />

        <p class="flex flex-wrap gap-x-3 text-[11px] text-muted">
            <span>{{ __('messages.moq') }} {{ $moq }}</span>
            <span>{{ __('messages.qty') }} {{ $item->quantity }}</span>
            @if ($item->sku)
                <span>{{ __('messages.sku') }} {{ $item->sku }}</span>
            @endif
        </p>

        <x-shop.button variant="secondary" size="sm" block class="mt-3"
            wire:click="{{ $cartAction }}"
            wire:loading.attr="disabled"
            wire:target="{{ $cartAction }}"
            :disabled="$isOutOfStock">
            <span wire:loading.remove wire:target="{{ $cartAction }}" class="inline-flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="size-4" />
                {{ __('messages.add_to_cart') }}
            </span>
            <x-filament::loading-indicator wire:loading wire:target="{{ $cartAction }}" class="size-4" />
        </x-shop.button>
    </div>
</article>
