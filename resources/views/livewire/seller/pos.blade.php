@php use Milon\Barcode\DNS1D; use Milon\Barcode\DNS2D; @endphp
<div class="flex flex-col lg:flex-row h-screen bg-gray-100 dark:bg-gray-900" x-data="{ cartOpen: false }">
    <!-- Mobile Cart Toggle Button -->
    <div class="lg:hidden fixed bottom-4 right-4 z-50">
        <button @click="cartOpen = !cartOpen" 
                class="bg-success-600 text-white p-3 rounded-full shadow-lg hover:bg-success-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            @if(!empty($cart ?? []))
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {{ count($cart ?? []) }}
                </span>
            @endif
        </button>
    </div>

    <!-- Products Section -->
    <div class="flex-1 lg:w-3/4 flex flex-col h-full overflow-hidden">
        <!-- Search and categories -->
        <div class="p-2 sm:p-4 bg-white dark:bg-gray-800 shadow">
            <div class="flex items-center mb-4">
                <a href="{{ route('filament.seller.pages.dashboard')}}"  class="p-2 me-2 sm:me-3 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                      </svg>
                </a>
                <div class="flex-1">
                    <div class="relative">
                        <input type="search" autofocus
                                               wire:model.live="search" 
                                               placeholder="Search your products here or scan barcode..." 
                                               class="w-full pl-8 sm:pl-10 pr-4 py-1.5 sm:py-2 text-sm sm:text-base rounded-lg border border-gray-300 dark:border-gray-700 focus:outline-none focus:border-none focus:ring-2 focus:outline-0 focus:ring-success-500"
                                               x-data="{
                                                   barcodeBuffer: '',
                                                   lastKeyTime: 0,
                                                   processingBarcode: false,
                                                   resetBuffer() { this.barcodeBuffer = ''; }
                                               }"
                                               x-on:keydown.enter="
                                                    $wire.searchProducts();
                                                    $wire.searchByBarcodeAndAddToCart(barcodeBuffer)
                                                                   .then(() => {
                                                                       $wire.set('search', '');
                                                                   });
                                               ">
                                        <div class="absolute left-2 sm:left-3 top-1.5 sm:top-2.5 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                    </div>
                </div>
                <button wire:click="$wire.reset()" class="ml-2 p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
            
            <div class="flex overflow-x-auto pb-2 space-x-2 scrollbar-hide">
                <button wire:click="filterByCategory(null)" 
                        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap {{ $selectedCategory === null ? 'bg-success-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                    All Decorations
                </button>
                @foreach($this->categories as $category)
                    <button wire:key="cte_{{ $category->id }}" wire:click="filterByCategory({{ $category->id }})" 
                            class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap {{ $selectedCategory === $category->id ? 'bg-success-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                        {{ $category->name }}
                        <sup class="text-gray-400 {{ $selectedCategory === $category->id ? 'bg-success-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">{{ $category->products_count }}</sup>
                    </button>
                @endforeach
            </div>
        </div>
        
        <!-- Products grid -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-2 sm:p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2 sm:gap-4">
                @forelse($this->products as $product)
                    <div wire:key="{{ $product->sku }}" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="bg-gray-200 dark:bg-gray-700 relative">
                            @if(($product->has_variant && $product->variants()->first() && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
                            <div class="absolute top-1 right-1 z-10">
                                <div class="bg-red-500 text-white px-2 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                                    <span class="font-bold text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->discount ?? 0 : $product->discount ?? '0' }}%
                                    </span>
                                    <span class="text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.off') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                          <div class="h-32 sm:h-40 overflow-hidden"> 
                            @if($product->productImg)
                                <img src="{{ $product->productImg }}" alt="{{ $product->name }}" class="w-full h-full hover:scale-125 transition rounded object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            </div>
                        </div>
                        <div class="p-2 sm:p-3">
                            <div class="flex justify-between items-start">
                                <h3 class="text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-2 mb-1" title="{{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->name : $product->name }}">
                                    {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->name : $product->name }}
                                </h3>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1 sm:gap-0 mt-1">
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 sm:h-3 sm:w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="text-xs">MOQ: {{ $product->MOQ }}</span>
                                </div>
                                
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 sm:px-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 sm:h-3 sm:w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    <span class="text-xs">Stock: {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->quantity ?? 0 : $product->quantity ?? 0 }}</span>
                                </div>
                            </div>
                
                            <div class="mt-2 sm:mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    @if($product->has_variant && $product->variants()->first())
                                        @if($product->variants()->first()->discount > 0)
                                            <div class="flex flex-col">
                                                <p class="text-sm sm:text-base text-success-600 dark:text-success-400 font-semibold">
                                                    {{ number_format($product->variants()->first()->mmk_discount_price, 0) }} Ks
                                                </p>
                                                <p class="text-gray-400 line-through text-xs">
                                                    {{ number_format($product->variants()->first()->mmk_price, 0) }}Ks
                                                </p>
                                            </div>
                                        @else
                                            <p class="text-sm sm:text-base text-gray-800 dark:text-gray-200 font-semibold">
                                                {{ number_format($product->variants()->first()->mmk_price, 0) }} Ks
                                            </p>
                                        @endif
                                    @else
                                        @if($product->discount > 0)
                                            <div class="flex flex-col">
                                                <p class="text-sm sm:text-base text-success-600 dark:text-success-400 font-semibold">
                                                    {{ number_format($product->mmk_discount_price, 0) }} Ks
                                                </p>
                                                <p class="text-gray-400 line-through text-xs">
                                                    {{ number_format($product->mmk_price, 0) }} ks
                                                </p>
                                            </div>
                                        @else
                                            <p class="text-sm sm:text-base text-gray-800 dark:text-gray-200 font-semibold">
                                                {{ number_format($product->mmk_price, 0) }} Ks
                                            </p>
                                        @endif
                                    @endif
                                </div>
                                
                                <div class="flex justify-center sm:block">
                                    @if($product->has_variant)
                                        {{ ($this->variantAction)(['record' => $product->id]) }}
                                    @else
                                        <x-filament::button
                                            size="sm"
                                            color="success"
                                            class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }} w-full sm:w-auto"
                                            wire:loading.attr="disabled"
                                            wire:click="addToCart({{ $product->id }},null,{{ $product->MOQ }})"
                                            wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                            <span wire:loading.remove wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </span>
                                            <span wire:loading wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                               
                                            </span>
                                        </x-filament::button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-12 px-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 shadow-sm text-center max-w-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">{{ __('messages.no_products') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or category filter</p>
                        </div>
                    </div>
                @endforelse
            </div>  
            <x-filament-actions::modals />
            
            <div class="p-2 sm:p-4 flex justify-center">
                @if($hasMorePages)
                <div
                    x-intersect.full="$wire.loadMore()"
                    wire:key="load-more"
                    class="p-4 w-full flex justify-center items-center"
                >
                    <x-filament::loading-indicator
                        wire:loading
                        wire:target="loadMore"
                        class="h-5 w-5"
                    />
                </div>
            @endif
            </div>
        </div>
    </div>

    <!-- Cart Section - Mobile overlay, desktop sidebar -->
    <div class="lg:w-1/4 w-full bg-white dark:bg-gray-800 shadow-lg flex flex-col 
                sm:fixed md:relative z-40 transform transition-transform duration-300 ease-in-out
                {{ $this->cart && count($this->cart) > 0 ? '' : 'lg:flex' }}"
         :class="{ 'translate-x-0': cartOpen, 'translate-x-full lg:translate-x-0': !cartOpen }"
         x-show="cartOpen || window.innerWidth >= 1024"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform translate-x-full">
        
        <!-- Mobile Cart Header -->
        <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cart</h2>
            <button @click="cartOpen = false" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Cart items -->
        <div class="flex-1 overflow-y-auto p-2 sm:p-4 space-y-2 sm:space-y-4">
            @forelse($this->cart ?? [] as $index=>$item)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 sm:pb-4" wire:key="{{ $item['name']}}_{{ $index}}">
                    <div class="flex-1">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white line-clamp-2">{{ $item['name'] }}</h3>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-2 gap-2">
                            <div class="flex items-center space-x-1 sm:space-x-2">
                                <button 
                                    wire:click="decrementQuantity({{ $index }})" 
                                    class="w-7 h-7 sm:w-6 sm:h-6 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none touch-manipulation"
                                    {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-sm font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                <button 
                                    wire:click="incrementQuantity({{ $index }})" 
                                    class="w-7 h-7 sm:w-6 sm:h-6 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none touch-manipulation"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item['price']) }} MMK</span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Item Total:</span>
                            <span class="text-sm font-semibold text-success-600 dark:text-success-400">
                                {{ number_format($item['subtotal']) }} MMK
                            </span>
                        </div>
                        @if(isset($item['has_discount']) && $item['has_discount'])
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-red-500 dark:text-red-400 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $item['discount_percentage'] }}% off
                            </span>
                            <span class="text-xs line-through text-gray-400 dark:text-gray-500">
                                {{ number_format($item['original_price']) }} MMK
                            </span>
                        </div>
                        @endif
                    </div>
                    <button wire:click="removeFromCart({{ $index }})" class="text-red-500 hover:text-red-700 p-1 ml-2 touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @empty
                <div class="text-center py-6 sm:py-8 px-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 sm:p-6 shadow-sm">
                        <div class="animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-700 dark:text-gray-200">Your cart is empty</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Add items to your cart to complete a sale</p>
                        <div class="mt-4 border-t border-dashed border-gray-200 dark:border-gray-600 pt-4">
                            <p class="text-xs text-success-500 dark:text-success-400">Tip: Use the barcode scanner or search for products</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Cart summary -->
        <div class="p-2 sm:p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <!-- Total amount card with shadow and gradient -->
            <div class="bg-gradient-to-r from-success-500 to-success-700 rounded-lg shadow-lg p-3 sm:p-4 mb-3 sm:mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-xs text-success-100 block mb-1">Cart Total</span>
                        <span class="text-xl sm:text-2xl text-white font-bebas space-x-1">{{ number_format($total ?? 0) }} MMK</span>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Payment Form (shown when showPaymentForm is true) -->
            @if($showPaymentForm)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 space-y-3 sm:space-y-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Payment
                    </h3>
                    
                    <!-- Total Amount Display -->
                    <div class="flex justify-between items-center p-2 sm:p-3 bg-white dark:bg-gray-800 rounded-lg">
                        <span class="font-medium text-gray-700 dark:text-gray-300 text-sm sm:text-base">Total Amount:</span>
                        <span class="text-base sm:text-lg font-bold text-success-600 dark:text-success-400">{{ number_format($total) }} MMK</span>
                    </div>
                    
                    <!-- Given Amount Input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Given Amount</label>
                        <div class="flex space-x-2">
                            <input type="number" 
                                   wire:model.live="givenAmount" 
                                   step="100"
                                   class="flex-1 px-3 py-2.5 sm:py-2 text-base sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-success-500 dark:bg-gray-800 dark:text-white touch-manipulation"
                                   placeholder="Enter given amount">
                            <button wire:click="setExactAmount" 
                                    class="px-3 sm:px-4 py-2.5 sm:py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 text-sm touch-manipulation">
                                Exact
                            </button>
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-3 gap-2 mt-2">
                            <button wire:click="$set('givenAmount', {{ ceil(($total ?? 0) / 1000) * 1000 }})" 
                                    class="px-2 sm:px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 text-xs sm:text-sm touch-manipulation">
                                {{ number_format(ceil(($total ?? 0) / 1000) * 1000) }}
                            </button>
                            <button wire:click="$set('givenAmount', {{ ceil(($total ?? 0) / 5000) * 5000 }})" 
                                    class="px-2 sm:px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 text-xs sm:text-sm touch-manipulation">
                                {{ number_format(ceil(($total ?? 0) / 5000) * 5000) }}
                            </button>
                            <button wire:click="$set('givenAmount', {{ ceil(($total ?? 0) / 10000) * 10000 }})" 
                                    class="px-2 sm:px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 text-xs sm:text-sm touch-manipulation">
                                {{ number_format(ceil(($total ?? 0) / 10000) * 10000) }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Change Amount Display -->
                    <div class="flex justify-between items-center p-2 sm:p-3 rounded-lg {{ $changeAmount >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                        <span class="font-medium text-sm sm:text-base {{ $changeAmount >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                            {{ $changeAmount >= 0 ? 'Change:' : 'Insufficient:' }}
                        </span>
                        <span class="text-base sm:text-lg font-bold {{ $changeAmount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format(abs($changeAmount)) }} MMK
                        </span>
                    </div>
                    
                    <!-- Payment Action Buttons -->
                    <div class="flex space-x-2">
                        <button wire:click="hidePayment" 
                                class="flex-1 px-4 py-2.5 sm:py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 text-sm touch-manipulation">
                            Cancel
                        </button>
                        <button wire:click="placeOrder" 
                                {{ $changeAmount < 0 ? 'disabled' : '' }}
                                class="flex-1 px-4 py-2.5 sm:py-2 bg-success-600 text-white rounded-lg hover:bg-success-500 disabled:opacity-50 disabled:cursor-not-allowed text-sm touch-manipulation">
                            <span wire:loading.remove wire:target="placeOrder">Complete Sale</span>
                            <span wire:loading wire:target="placeOrder">Processing...</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Show Payment Button when payment form is hidden -->
                <button wire:click="showPayment"
                        class="w-full inline-flex justify-center items-center px-4 py-3 sm:py-2.5 bg-success-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wider hover:bg-success-500 active:bg-success-700 focus:outline-none focus:border-success-700 focus:ring focus:ring-success-300 disabled:opacity-25 transition touch-manipulation"
                        @if(empty($cart ?? []))
                            disabled
                        @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Proceed to Payment
                </button>
            @endif
        </div>
    </div>

    <!-- Mobile overlay backdrop -->
    <div class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"
         x-show="cartOpen"
         @click="cartOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>
   
   <x-pos-invoice :showReceipt="$showReceipt" :lastOrder="$lastOrder" />

</div>

