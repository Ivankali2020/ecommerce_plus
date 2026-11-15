@php
    $isActive = request()->routeIs('cart');
@endphp

<div @class(['contents' => $variant === 'mobile'])>
    @if ($variant === 'mobile')
        <x-shop.bottom-nav-item :href="route('cart')" icon="heroicon-o-shopping-bag"
            :label="__('messages.navigation.cart')" :active="$isActive">
            @if ($this->cartCount > 0)
                <span class="absolute right-1/2 top-1.5 translate-x-4 rounded-full bg-accent px-1.5 text-[10px] font-medium leading-4 text-white tabular-nums">
                    {{ $this->cartCount }}
                </span>
            @endif
        </x-shop.bottom-nav-item>
    @else
        <a href="{{ route('cart') }}" wire:navigate
            aria-label="{{ __('messages.navigation.cart') }}"
            @class([
                'relative flex size-10 items-center justify-center rounded-full transition-colors hover:bg-ink/5',
                'text-ink' => $isActive,
                'text-ink-soft hover:text-ink' => ! $isActive,
            ])>
            <x-heroicon-o-shopping-bag class="size-[22px]" />

            @if ($this->cartCount > 0)
                <span x-data="{ bump: false }"
                    x-on:cart-updated.camel.window="bump = true; setTimeout(() => bump = false, 600)"
                    :class="bump && 'scale-125'"
                    class="absolute -right-0.5 -top-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-accent px-1 text-[10px] font-medium text-white tabular-nums transition-transform duration-300">
                    {{ $this->cartCount }}
                </span>
            @endif
        </a>
    @endif
</div>
