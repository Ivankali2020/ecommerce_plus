{{--
    One cart row. The host Livewire component provides `updateQuantity(id, qty)` and `removeItem(id)`.
--}}
@props(['item'])

@php
    $priced = $item->variant ?? $item->product;
    $hasDiscount = $priced->discount > 0;
    $lineTotal = $item->price * $item->quantity;
    $originalLineTotal = $priced->mmk_price * $item->quantity;
    $attributeValues = $priced->attributeValues ?? [];

    $decreaseAction = "updateQuantity({$item->id}, ".($item->quantity - 1).')';
    $increaseAction = "updateQuantity({$item->id}, ".($item->quantity + 1).')';
    $removeAction = "removeItem({$item->id})";
@endphp

<article wire:key="cart-item-{{ $item->id }}" {{ $attributes->class('flex gap-4 py-6 animate-fade-in md:gap-6') }}>
    <a href="{{ route('product', $item->product->id) }}" wire:navigate
        class="block size-24 shrink-0 overflow-hidden rounded-xl bg-accent-soft/50 md:size-28">
        <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}" class="size-full object-cover">
    </a>

    <div class="flex min-w-0 flex-1 flex-col gap-3">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 space-y-1">
                @if ($item->product->category)
                    <p class="text-[10px] uppercase tracking-[0.2em] text-muted">{{ $item->product->category->name }}</p>
                @endif
                <h3 class="line-clamp-2 text-sm text-ink md:text-base">
                    <a href="{{ route('product', $item->product->id) }}" wire:navigate class="hover:text-accent-dark">{{ $item->product->name }}</a>
                </h3>
                @if ($item->variant)
                    <p class="text-xs text-ink-soft">{{ __('messages.cart.variant') }}: {{ $item->variant->name }}</p>
                @endif
            </div>

            <button type="button" wire:click="{{ $removeAction }}" wire:loading.attr="disabled" wire:target="{{ $removeAction }}"
                aria-label="{{ __('messages.cart.remove_item') }}"
                class="-me-2 -mt-1 flex size-8 shrink-0 items-center justify-center rounded-full text-muted transition-colors hover:bg-sale/5 hover:text-sale">
                <x-heroicon-o-x-mark wire:loading.remove wire:target="{{ $removeAction }}" class="size-4" />
                <x-filament::loading-indicator wire:loading wire:target="{{ $removeAction }}" class="size-4" />
            </button>
        </div>

        <x-shop.attribute-chips :items="$attributeValues" />

        <div class="mt-auto flex flex-wrap items-end justify-between gap-3">
            <div class="flex h-9 items-center rounded-full border border-line">
                <button type="button" wire:click="{{ $decreaseAction }}" wire:loading.attr="disabled" wire:target="{{ $decreaseAction }}"
                    aria-label="−" class="flex size-9 items-center justify-center text-ink-soft hover:text-ink">
                    <x-heroicon-m-minus wire:loading.remove wire:target="{{ $decreaseAction }}" class="size-3.5" />
                    <x-filament::loading-indicator wire:loading wire:target="{{ $decreaseAction }}" class="size-3.5" />
                </button>
                <span class="w-8 text-center text-sm tabular-nums text-ink">{{ $item->quantity }}</span>
                <button type="button" wire:click="{{ $increaseAction }}" wire:loading.attr="disabled" wire:target="{{ $increaseAction }}"
                    aria-label="+" class="flex size-9 items-center justify-center text-ink-soft hover:text-ink">
                    <x-heroicon-m-plus wire:loading.remove wire:target="{{ $increaseAction }}" class="size-3.5" />
                    <x-filament::loading-indicator wire:loading wire:target="{{ $increaseAction }}" class="size-3.5" />
                </button>
            </div>

            <div class="text-end">
                <p class="text-[11px] text-muted tabular-nums">{{ $item->quantity }} × {{ number_format($item->price) }}</p>
                <x-shop.price :amount="$lineTotal" :original="$hasDiscount ? $originalLineTotal : null" size="sm" class="justify-end" />
            </div>
        </div>
    </div>
</article>
