<div>
    <!-- Desktop Cart Link -->
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-[100%]">
        <x-nav-link :href="route('cart')" :active="request()->routeIs('cart')" class="relative">
            {{ __('messages.navigation.cart') }}
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                    {{ $cartCount }}
                </span>
            @endif
        </x-nav-link>
    </div>

    <!-- Mobile Cart Link -->
    <a href="{{ route('cart') }}" class="inline-flex lg:hidden p-2 flex-col items-center justify-center px-1 hover:bg-gray-50 {{ request()->routeIs('cart') ? 'text-[#c2a03d]' : 'text-gray-500' }} relative">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
        </svg>
        <span class="text-xs">{{ __('messages.navigation.cart') }}</span>
        @if($cartCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $cartCount }}
            </span>
        @endif
    </a>
</div>
