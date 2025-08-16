<div class="w-full p-4 lg:p-0 lg:w-2/3" x-data="{ showCartItems: true }">
    <div class="flex items-center justify-between mb-4 md:mb-6">
        <h2 class="text-2xl md:text-3xl font-bebas {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
            {{ __('messages.cart.shopping_cart') }}
        </h2>
        
        <!-- Single Toggle Button -->
        <div class="flex gap-2">
            <button 
                @click="showCartItems = !showCartItems"
                class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors flex items-center gap-2 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                :class="showCartItems ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100  border-slate-200 text-black '"
            >
                <!-- Receipt Icon -->
                <svg x-show="showCartItems" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                
                <!-- Shopping Cart Icon -->
                <svg x-show="!showCartItems" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                
                <span x-text="showCartItems ? 'Show Receipt' : 'Show Items'"></span>
            </button>
        </div>
    </div>

    <!-- Cart Items Section -->
    <div x-show="showCartItems" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">

    @forelse($cartItems as $item)
        <div wire:key="cart-item-{{ $item->id }}"
            class="relative bg-white rounded-lg shadow-sm border border-gray-100 p-3 md:p-4 mb-3 md:mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }} animate-fade-in-up">
            
            <!-- Mobile Layout (sm and below) -->
            <div class="block md:hidden">
                <div class="flex gap-3">
                    <!-- Product Image - Mobile -->
                    <div class="flex-shrink-0">
                        <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                            class="w-16 h-16 object-cover rounded-lg">
                    </div>
                    
                    <!-- Product Info - Mobile -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold line-clamp-2 mb-1">{{ $item->product->name }}</h3>
                        <p class="text-xs text-gray-500 mb-2">{{ $item->product->category?->name }}</p>
                        
                        <!-- Price - Mobile -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ number_format($item->price * $item->quantity) }} Ks
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $item->quantity }} x {{ number_format($item->variant ? $item->variant->mmk_discount_price : $item->product->mmk_discount_price) }} Ks
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remove Button - Mobile -->
                    <button wire:click="removeItem({{ $item->id }})"
                        class="flex-shrink-0 p-1 text-red-500 hover:text-red-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <!-- Variant/Attributes - Mobile -->
                @if ($item->variant)
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-600 mb-1">Variant: {{ $item->variant->name }}</p>
                        @if($item->variant->attributeValues)
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->variant->attributeValues as $key => $value)
                                    <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs">
                                        {{ $value['attribute'] }}: {{ implode(', ', $value['value']) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    @if($item->product->attributeValues)
                        <div class="mt-2 pt-2 border-t border-gray-100">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($item->product->attributeValues as $attributeValue)
                                    <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs">
                                        {{ $attributeValue['attribute'] }}: {{ implode(', ', $attributeValue['value']) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
                
                <!-- Quantity Controls - Mobile -->
                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                            class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3 h-3">
                                <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z" />
                            </svg>
                        </button>
                        <span class="w-8 text-center text-sm font-medium">{{ $item->quantity }}</span>
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                            class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3 h-3">
                                <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                            </svg>
                        </button>
                    </div>
                    
                    @if (($item->variant && $item->variant->discount > 0) || (!$item->variant && $item->product->discount > 0))
                        <div class="text-right">
                            <p class="text-xs text-gray-400 line-through">
                                {{ number_format(($item->variant ? $item->variant->mmk_price : $item->product->mmk_price) * $item->quantity) }} Ks
                            </p>
                            <p class="text-xs text-red-500 font-medium">
                                {{ $item->variant ? $item->variant->discount : $item->product->discount }}% OFF
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Desktop Layout (md and above) -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Product Image - Desktop -->
                <div class="flex-shrink-0">
                    <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                        class="w-20 h-20 lg:w-24 lg:h-24 object-cover rounded-lg">
                </div>

                <!-- Product Details - Desktop -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base lg:text-lg font-bold line-clamp-2 mb-1">{{ $item->product->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ $item->product->category?->name }}</p>

                    @if ($item->variant)
                        <p class="text-sm text-gray-600 mb-1">Variant: {{ $item->variant->name }}</p>
                        @if($item->variant->attributeValues)
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->variant->attributeValues as $key => $value)
                                    <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs font-medium">
                                        {{ $value['attribute'] }}: {{ implode(', ', $value['value']) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @else
                        @if($item->product->attributeValues)
                            <div class="flex flex-wrap gap-1">
                                @foreach ($item->product->attributeValues as $attributeValue)
                                    <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs">
                                        {{ $attributeValue['attribute'] }}: {{ implode(', ', $attributeValue['value']) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Quantity Controls - Desktop -->
                <div class="flex items-center gap-3">
                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                            <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z" />
                        </svg>
                    </button>
                    <span class="w-12 text-center font-medium">{{ $item->quantity }}</span>
                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                        </svg>
                    </button>
                </div>

                <!-- Price - Desktop -->
                <div class="text-right min-w-[120px] lg:min-w-[140px]">
                    <p class="text-xs text-gray-400 mb-1">
                        {{ $item->quantity }} x {{ number_format($item->variant ? $item->variant->mmk_discount_price : $item->product->mmk_discount_price) }} Ks
                    </p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ number_format($item->price * $item->quantity) }} Ks
                    </p>
                    @if (($item->variant && $item->variant->discount > 0) || (!$item->variant && $item->product->discount > 0))
                        <div class="mt-1">
                            <p class="text-sm text-gray-400 line-through">
                                {{ number_format(($item->variant ? $item->variant->mmk_price : $item->product->mmk_price) * $item->quantity) }} Ks
                            </p>
                            <p class="text-xs text-red-500 font-medium">
                                {{ $item->variant ? $item->variant->discount : $item->product->discount }}% OFF
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Remove Button - Desktop -->
                <button wire:click="removeItem({{ $item->id }})"
                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
               
    @empty
        <div class="text-center py-8 md:py-12 animate-fade-in">
            <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-sm md:text-base font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.cart.empty_cart') }}
            </h3>
            <p class="mt-1 text-xs md:text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.cart.start_adding') }}
            </p>
            <div class="mt-4 md:mt-6">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-0 focus:ring-offset-0 transition-colors {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.cart.continue_shopping') }}
                </a>
            </div>
        </div>
    @endforelse
    </div>

    <!-- Invoice Receipt Section -->
    <div x-show="!showCartItems && {{ $cartItems->isNotEmpty() ? 'true' : 'false' }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
        @if($cartItems->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 md:p-6 animate-fade-in font-mono ">
            <h3 class="text-xl font-bebas mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                Invoice Receipt
            </h3>
            
            <div class="space-y-4">
                <!-- Receipt Header -->
                <div class="border-b pb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-lg">Shopping Cart Receipt</h4>
                            <p class="text-sm text-gray-600">Date: {{ now()->format('d M, Y H:i A') }}</p>
                            @if($delivery && isset($delivery['type']))
                            <p class="text-sm text-gray-600">Delivery: {{ ucfirst($delivery['type']) }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Items: {{ $cartItems->count() }}</p>
                            <p class="text-sm text-gray-600">Total Qty: {{ $cartItems->sum('quantity') }}</p>
                            @if($delivery && isset($delivery['fee']) && $delivery['fee'] > 0)
                            <p class="text-sm text-gray-600">Delivery Fee: {{ number_format($delivery['fee']) }} Ks</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Receipt Items -->
                <div class="space-y-2">
                    @foreach($cartItems as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <div class="flex-1">
                            <h5 class="font-medium text-sm line-clamp-1">{{ $item->product->name }}</h5>
                            @if($item->variant)
                                <p class="text-xs text-gray-500">Variant: {{ $item->variant->name }}</p>
                            @endif
                            <p class="text-xs text-gray-500">
                                {{ $item->quantity }} x {{ number_format($item->variant ? $item->variant->mmk_discount_price : $item->product->mmk_discount_price) }} Ks
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ number_format($item->price * $item->quantity) }} Ks</p>
                            @if (($item->variant && $item->variant->discount > 0) || (!$item->variant && $item->product->discount > 0))
                                <p class="text-xs text-red-500">
                                    {{ $item->variant ? $item->variant->discount : $item->product->discount }}% OFF
                                </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Receipt Total -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Subtotal:</span>
                        <span class="font-medium">{{ number_format($cartItems->sum(fn($item) => $item->price * $item->quantity)) }} Ks</span>
                    </div>
                    
                    @if($delivery && isset($delivery['fees']) && $delivery['fees'] > 0)
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">Delivery Fees:</span>
                        <span class="text-gray-600">{{ number_format($delivery['fees']) }} Ks</span>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Grand Total:</span>
                        <span class="font-bold text-lg">{{ number_format($cartItems->sum(fn($item) => $item->price * $item->quantity) + $delivery['fees']) }} Ks</span>
                    </div>
                    @else
                    <p class="text-xs text-gray-500 mt-2">
                        *Delivery fees and final total will be calculated at checkout
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
