<div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Left sidebar - Categories and Products -->
    <div class="w-3/4 flex flex-col h-full overflow-hidden">
        <!-- Search and categories -->
        <div class="p-4 bg-white dark:bg-gray-800 shadow">
            <div class="flex items-center mb-4">
                <a href="{{ route('filament.seller.pages.dashboard')}}"  class="ml-2 p-2 me-3 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                      </svg>
                </a>
                <div class="flex-1">
                    <div class="relative">
                        <input type="search" autofocus
                                               wire:model.live="search" 
                                               placeholder="Search your products here or scan barcode..." 
                                               class="w-full pl-10 pr-4 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700 focus:outline-none focus:border-none focus:ring-2 focus:outline-0 focus:ring-primary-500 "
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
                                        <div class="absolute left-3 top-2.5 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                    </div>
                </div>
                <button wire:click="$wire.reset()" class="ml-2 p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
            
            <div class="flex overflow-x-auto pb-2 space-x-2">
                <button wire:click="filterByCategory(null)" 
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $selectedCategory === null ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                    All Decorations
                </button>
                @foreach($this->categories as $category)
                    <button wire:key="cte_{{ $category->id }}" wire:click="filterByCategory({{ $category->id }})" 
                            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $selectedCategory === $category->id ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                        {{ $category->name }}
                        <sup class="text-gray-400 ">{{ $category->products_count }}</sup>
                    </button>
                @endforeach
            </div>
        </div>
        
        <!-- Products grid -->
        <div class="flex-1  overflow-y-auto ">
            <div class="p-4  grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-6 gap-4">
                @forelse($this->products as $product)
                    <div wire:key="{{ $product->sku }}" class="bg-white  dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class=" bg-gray-200 dark:bg-gray-700 relative">
                            @if(($product->has_variant && $product->variants()->first() && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
                            <div class="absolute top-0 right-0 z-10">
                                <div class="bg-red-500 text-white px-3 py-1 rounded-full shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                                    <span class="font-bold text-xs  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->discount ?? 0 : $product->discount ?? '0' }}%
                                    </span>
                                    <span class="text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.off') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                            @if($product->productImg)
                                <img src="{{ $product->productImg }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-3 ">
                            <div class="flex justify-between items-start">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-2 mb-1" title="{{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->name : $product->name }}">
                                    {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->name : $product->name }}
                                </h3>
                            </div>
                            
                            <div class="flex justify-between mt-1">
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span>MOQ: {{ $product->MOQ }}</span>
                                </div>
                                
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    <span>Stock: {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->quantity ?? 0 : $product->quantity ?? 0 }}</span>
                                </div>
                            </div>
                
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    @if($product->has_variant && $product->variants()->first())
                                        @if($product->variants()->first()->discount > 0)
                                            <div class="flex flex-col  ">
                                                <p class="text-primary-600 dark:text-primary-400 font-semibold">
                                                    {{ number_format($product->variants()->first()->mmk_discount_price, 0) }} Ks
                                                </p>
                                                <p class="text-gray-400 line-through text-xs">
                                                    {{ number_format($product->variants()->first()->mmk_price, 0)  }}Ks
                                                </p>
                                            </div>
                                        @else
                                            <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                {{ number_format($product->variants()->first()->mmk_price, 0) }} Ks
                                            </p>
                                        @endif
                                    @else
                                        @if($product->discount > 0)
                                            <div class="flex flex-col  items-center gap-2">
                                                <p class="text-primary-600 dark:text-primary-400 font-semibold">
                                                    {{ number_format($product->mmk_discount_price, 0) }} Ks
                                                </p>
                                                <p class="text-gray-400 line-through text-xs">
                                                    {{ number_format($product->mmk_price, 0) }}
                                                </p>
                                            </div>
                                        @else
                                            <p class="text-gray-800 dark:text-gray-200 font-semibold">
                                                {{ number_format($product->mmk_price, 0) }} Ks
                                            </p>
                                        @endif
                                    @endif
                                </div>
                                
                                <div>
                                    @if($product->has_variant)
                                        {{ ($this->variantAction)(['record' => $product->id]) }}
                                    @else
                                        <x-filament::button
                                            size="sm"
                                            color="primary"
                                            class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                                            wire:loading.attr="disabled"
                                            wire:click="addToCart({{ $product->id }},null,{{ $product->MOQ }})"
                                            wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                            <span wire:loading.remove wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </span>
                                            <span wire:loading wire:target="addToCart({{ $product->id }},null,{{ $product->MOQ }})">
                                                <x-filament::loading-indicator class="h-4 w-4" />
                                            </span>
                                        </x-filament::button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div>{{ __('messages.no_products') }}</div>
                @endforelse
            </div>  
            <x-filament-actions::modals />
            
            <div class="p-4 flex justify-center">
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

    <!-- Right sidebar - Cart -->
    <div class="w-1/4 bg-white dark:bg-gray-800 shadow-lg flex flex-col h-full">
        {{-- <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Detail Items</h2>
        </div>
         --}}
        <!-- Cart items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($this->cart ?? [] as $index=>$item)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-4" wire:key="{{ $item['name']}}_{{ $index}}">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center space-x-2">
                                <button 
                                    wire:click="decrementQuantity({{ $index }})" 
                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none"
                                    {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-sm font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                <button 
                                    wire:click="incrementQuantity({{ $index }})" 
                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item['price']) }} MMK</span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Item Total:</span>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
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
                    <button wire:click="removeFromCart({{ $index }})" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @empty
                <div class="text-center py-8 px-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 shadow-sm">
                        <div class="animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-200">Your cart is empty</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Add items to your cart to complete a sale</p>
                        <div class="mt-4 border-t border-dashed border-gray-200 dark:border-gray-600 pt-4">
                            <p class="text-xs text-primary-500 dark:text-primary-400">Tip: Use the barcode scanner or search for products</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Cart summary -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Total amount card with shadow and gradient -->
            <div class="bg-gradient-to-r from-primary-500 to-primary-700 rounded-lg shadow-lg p-4 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-xs text-primary-100 block mb-1">Cart Total</span>
                        <span class="text-2xl  text-white font-bebas space-x-1 ">{{ number_format($total ?? 0) }} MMK</span>
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <button 
                wire:click="placeOrder"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wider hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-300 disabled:opacity-25 transition  "
                @if(empty($cart ?? []))
                    disabled
                @endif
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span wire:loading.remove wire:target="placeOrder">Complete Sale</span>
                <span wire:loading wire:target="placeOrder">Processing...</span>
            </button>
        </div>
    </div>
   
</div>

