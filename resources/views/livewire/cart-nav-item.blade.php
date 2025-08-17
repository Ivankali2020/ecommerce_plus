<div>
    <!-- Desktop Cart Link -->
    <div class="hidden sm:-my-px sm:flex h-full">
        <x-nav-link :href="route('cart')" :active="request()->routeIs('cart')" class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
              </svg>
            @if($cartCount > 0)
                <span 
                    x-data="{ animate: false }" 
                    x-on:cart-updated.window="animate = true; setTimeout(() => animate = false, 1000)"
                    :class="animate ? 'animate-bounce' : ''"
                    class="absolute top-0 -right-5 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white  bg-red-600 rounded-full">
                    {{ $cartCount }}
                </span>
            @endif
        </x-nav-link>
    </div>

    <!-- Mobile Cart Link -->
    <a href="{{ route('cart') }}" class="inline-flex md:hidden lg:hidden p-2 flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('cart') ? 'text-[#c2a03d]' : 'text-gray-500' }} relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
        <span class="text-xs">{{ __('messages.navigation.cart') }}</span>
        @if($cartCount > 0)
            <span 
                x-data="{ animate: false }" 
                x-on:cart-updated.window="animate = true; setTimeout(() => animate = false, 1000)"
                :class="animate ? 'animate-bounce' : ''"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $cartCount }}
            </span>
        @endif
    </a>
</div>
