{{--
    Product tile used on the home page, product listing and related products.
    The host Livewire component must use the WithCart trait (provides `addToCart`).
--}}
@props([
    'product',
    'type' => 'latest',
])

@php
    $firstVariant = $product->has_variant && $product->relationLoaded('variants') ? $product->variants->first() : null;
    $priced = $firstVariant ?? $product;

    $displayName = $firstVariant?->name ?? $product->name;
    $hasDiscount = $priced->discount > 0;
    $currentPrice = $hasDiscount ? $priced->mmk_discount_price : $priced->mmk_price;

    $isOutOfStock = ! $product->has_variant && $product->quantity <= 0;
    $stockQuantity = $product->has_variant ? null : $product->quantity;
    $categoryName = $product->relationLoaded('category') ? $product->category?->name : null;

    $addToCartTarget = "addToCart({$product->id})";
@endphp

<article wire:key="product-card-{{ $type }}-{{ $product->id }}" {{ $attributes->class('group flex flex-col animate-fade-in-up') }}>
    <a href="{{ route('product', $product->id) }}" wire:navigate
        class="relative block aspect-[4/5] overflow-hidden rounded-2xl bg-accent-soft/50">
        <img src="{{ $product->productImgThumb }}"
            srcset="{{ $product->productImg }} 800w"
            alt="{{ $displayName }}"
            loading="lazy"
            class="size-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]" />

        <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
            @if ($hasDiscount)
                <x-shop.badge tone="sale">−{{ $priced->discount }}%</x-shop.badge>
            @else
                <span></span>
            @endif

            @if ($type === 'wholesale')
                <x-shop.badge tone="light">{{ __('messages.wholesale.wholesale') }}</x-shop.badge>
            @endif
        </div>

        @if ($isOutOfStock)
            <div class="absolute inset-0 flex items-center justify-center bg-canvas/60 backdrop-blur-[2px]">
                <x-shop.badge tone="dark">{{ __('messages.out_of_stock') }}</x-shop.badge>
            </div>
        @endif
    </a>

    <div class="flex grow flex-col gap-1.5 pt-4">
        @if ($categoryName)
            <p class="text-[10px] uppercase tracking-[0.2em] text-muted md:text-[11px]">{{ $categoryName }}</p>
        @endif

        <h3 class="line-clamp-2 text-sm leading-snug text-ink md:text-base">
            <a href="{{ route('product', $product->id) }}" wire:navigate class="hover:text-accent-dark">{{ $displayName }}</a>
        </h3>

        <x-shop.price :amount="$currentPrice" :original="$hasDiscount ? $priced->mmk_price : null" size="sm" class="mt-auto pt-1" />

        <p class="flex flex-wrap gap-x-3 text-[11px] text-muted">
            <span>{{ __('messages.moq') }} {{ $product->MOQ }}</span>
            @if ($stockQuantity > 0)
                <span>{{ $stockQuantity }} {{ __('messages.in_stock') }}</span>
            @endif
        </p>

        <x-shop.button variant="secondary" size="sm" block class="mt-3"
            wire:click="{{ $addToCartTarget }}"
            wire:loading.attr="disabled"
            wire:target="{{ $addToCartTarget }}"
            :disabled="$isOutOfStock">
            <span wire:loading.remove wire:target="{{ $addToCartTarget }}" class="inline-flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="size-4" />
                {{ __('messages.add_to_cart') }}
            </span>
            <span wire:loading wire:target="{{ $addToCartTarget }}">{{ __('messages.adding_to_cart') }}</span>
        </x-shop.button>
    </div>
</article>
