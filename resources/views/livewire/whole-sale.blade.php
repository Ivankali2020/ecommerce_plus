<div class="container mb-20 lg:mb-0 mx-auto px-4 py-4 lg:py-8" x-data="{ showFilters: false, showCategories: true, showBrands: true }">
    <div class="flex justify-between items-center mb-4 lg:mb-8">
        <h2 class="text-xl lg:text-3xl mb-0 ">
            {{ __('messages.wholesale.title', ['count' => $totalProducts]) }}
        </h2>
        
        <!-- Mobile Filter Toggle -->
        <button @click="showFilters = !showFilters" class="lg:hidden bg-slate-900 text-white px-4 py-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        </button>
    </div>


    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Filters Sidebar -->
        <div class="w-full lg:w-1/4" :class="showFilters || window.innerWidth >= 1024 ? 'block' : 'hidden'">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold ">
                        {{ __('messages.filters') }}
                    </h3>
                    @if($search || $selectedCategory || $selectedBrand || $minPrice || $maxPrice || $availableOnly)
                    <button 
                        wire:click="clearAllFilters" 
                        class="text-sm text-red-600 hover:text-red-800 "
                    >
                        {{ __('messages.clear_all') }}
                    </button>
                    @endif
                </div>

                <!-- Search Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.search') }}
                    </label>
                    <div class="relative">
                        <x-text-input 
                            wire:model.live.debounce.500ms="search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg "
                            placeholder="{{ __('messages.search_wholesale') }}" />
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.price_range') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <x-text-input 
                            wire:model.live.debounce.500ms="minPrice"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm "
                            placeholder="{{ __('messages.min_price') }}"
                            min="0" />
                        <x-text-input
                            wire:model.live.debounce.500ms="maxPrice"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm "
                            placeholder="{{ __('messages.max_price') }}"
                            min="0" />
                    </div>
                    @if($minPrice || $maxPrice)
                    <button 
                        wire:click="clearPriceFilter" 
                        class="mt-2 text-xs text-red-600 hover:text-red-800"
                    >
                        {{ __('messages.clear_price_filter') }}
                    </button>
                    @endif
                </div>

                <!-- Category Filter -->
                @if($categories->count() > 0)
                <div class="mb-6">
                    <button 
                        @click="showCategories = !showCategories"
                        class="w-full flex justify-between items-center text-sm font-medium text-gray-700 mb-3  hover:text-gray-900 transition-colors duration-200"
                    >
                        <span>{{ __('messages.categories') }}</span>
                        <div class="shrink-0 ml-2">
                            <svg 
                                class="w-4 h-4 text-gray-500 transform transition-transform duration-300 ease-in-out" 
                                :class="showCategories ? 'rotate-180' : 'rotate-0'"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div 
                        class="overflow-hidden transition-all duration-300 ease-in-out" 
                        :class="showCategories ? 'max-h-screen opacity-100' : 'max-h-0 opacity-0'"
                    >
                        <div class="space-y-2">
                            <!-- Clear category option -->
                            @if($selectedCategory)
                            <div class="pb-2 border-b border-gray-200 mb-2">
                                <button 
                                    wire:click="$set('selectedCategory', null)"
                                    class="text-sm text-red-600 hover:text-red-800  px-2 py-1 rounded hover:bg-red-50"
                                >
                                    ✕ {{ __('messages.clear_category') }}
                                </button>
                            </div>
                            @endif
                            
                            @foreach($categories as $category)
                            <label for="{{ 'category_label' . $category->id }}" class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer" wire:key="category-{{ $category->id }}">
                                <input 
                                    id="{{ 'category_label' . $category->id }}"
                                    type="radio" 
                                    name="category_filter"
                                    value="{{ $category->id }}"
                                    wire:model.live="selectedCategory"
                                    class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-0 focus:outline-none"
                                >
                                <span class="ml-2 text-sm ">
                                    {{ $category->name }}
                                    <span class="text-gray-500">({{ $category->products_count }})</span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Brand Filter -->
                @if($brands->count() > 0)
                <div class="mb-6">
                    <button 
                        @click="showBrands = !showBrands"
                        class="w-full flex justify-between items-center text-sm font-medium text-gray-700 mb-3  hover:text-gray-900 transition-colors duration-200"
                    >
                        <span>{{ __('messages.brands') }}</span>
                        <div class="shrink-0 ml-2">
                            <svg 
                                class="w-4 h-4 text-gray-500 transform transition-transform duration-300 ease-in-out" 
                                :class="showBrands ? 'rotate-180' : 'rotate-0'"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div 
                        class="overflow-hidden transition-all duration-300 ease-in-out" 
                        :class="showBrands ? 'max-h-screen opacity-100' : 'max-h-0 opacity-0'"
                    >
                        <div class="space-y-2">
                            <!-- Clear brand option -->
                            @if($selectedBrand)
                            <div class="pb-2 border-b border-gray-200 mb-2">
                                <button 
                                    wire:click="$set('selectedBrand', null)"
                                    class="text-sm text-red-600 hover:text-red-800  px-2 py-1 rounded hover:bg-red-50"
                                >
                                    ✕ {{ __('messages.clear_brand') }}
                                </button>
                            </div>
                            @endif
                            
                            @foreach($brands as $brand)
                            <label class="flex items-center p-2  rounded-lg hover:bg-gray-50 cursor-pointer" wire:key="brand-{{ $brand->id }}">
                                <input 
                                    type="radio" 
                                    name="brand_filter"
                                    value="{{ $brand->id }}"
                                    wire:model.live="selectedBrand"
                                    class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-0 focus:outline-none"
                                >
                                <span class="ml-2 text-sm ">
                                    {{ $brand->name }}
                                    <span class="text-gray-500">({{ $brand->products_count }})</span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Additional Options -->
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:click="toggleAvailableOnly"
                            {{ $availableOnly ? 'checked' : '' }}
                            class="h-4 w-4 text-slate-600 border-gray-300 rounded"
                        >
                        <span class="ml-2 text-sm ">
                            {{ __('messages.available_only') }}
                        </span>
                    </label>
                    
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:click="toggleShowVariants"
                            {{ $showVariants ? 'checked' : '' }}
                            class="h-4 w-4 text-slate-600 border-gray-300 rounded"
                        >
                        <span class="ml-2 text-sm ">
                            {{ __('messages.show_variants') }}
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Sort Controls -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700 ">
                            {{ __('messages.sort_by') }}:
                        </span>
                        <div class="flex gap-2">
                            <button 
                                wire:click="setSortBy('name')"
                                class="px-3 py-1 text-sm rounded-full transition-colors {{ $sortBy === 'name' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} "
                            >
                                {{ __('messages.name') }}
                                @if($sortBy === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                            <button 
                                wire:click="setSortBy('price')"
                                class="px-3 py-1 text-sm rounded-full transition-colors {{ $sortBy === 'price' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} "
                            >
                                {{ __('messages.price') }}
                                @if($sortBy === 'price')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                            <button 
                                wire:click="setSortBy('created_at')"
                                class="px-3 py-1 text-sm rounded-full transition-colors {{ $sortBy === 'created_at' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} "
                            >
                                {{ __('messages.newest') }}
                                @if($sortBy === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-sm hidden md:block text-gray-600 ">
                        {{ __('messages.showing_results', ['count' => $products->count() + ($showVariants ? $variants->count() : 0)]) }}
                    </div>
                </div>
            </div>

            <!-- Results -->
            @if($products->count() > 0 || ($showVariants && $variants->count() > 0))
                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="mb-8" >
                    <h3 class="text-lg font-semibold mb-4 ">
                        {{ __('messages.wholesale.products') }} ({{ $products->count() }})
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 lg:4 2xl:gap-6">
                        @foreach($products as $product)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300" wire:key="product-{{ $product->id }}">
                            <a href="{{ route('product', $product->id ) }}" class="relative">
                                <img
                                    src="{{ $product->productImgThumb ?: asset('images/placeholder.jpg') }}"
                                    data-src="{{ $product->productImg ?: asset('images/placeholder.jpg') }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-40 md:h-48 lg:52 2xl:h-60 object-cover lazyload blur-sm"
                                    loading="lazy"
                                >
                                <div class="absolute top-2 right-2">
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ __('messages.wholesale.wholesale') }}
                                    </span>
                                </div>
                                @if($product->quantity <= 0)
                                <div class="absolute inset-0 bg-black/30 backdrop-blur-xs flex items-center justify-center">
                                    <span class="text-white font-semibold ">
                                        {{ __('messages.out_of_stock') }}
                                    </span>
                                </div>
                                @endif
                                <div class="text-sm  absolute bottom-2 left-2 text-gray-600 mt-2 md:mt-0 self-end ">
                                    {{ __('messages.qty') }}: {{ $product->quantity }}
                                </div>
                            </a>
                            
                            <div class="px-2 md:px-4 py-4">
                                <h4 class="text-sm md:text-lg font-semibold text-gray-900 mb-2 ">
                                    {{ $product->name }}
                                </h4>
                                
                                <div class="text-xs text-gray-600 mb-2 ">
                                    <span>{{ $product->category->name }}</span>
                                    @if($product->brand)
                                    <span class="mx-1">•</span>
                                    <span>{{ $product->brand->name }}</span>
                                    @endif
                                </div>
                                
                                    <div class="mb-2">
                                        @if($product->discount > 0)
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs line-through  font-bold text-slate-300">
                                                {{ number_format($product->mmk_price) }} <span class="text-xs hidden md:block">{{ __('messages.mmk') }}</span>
                                            </span>
                                            <span class="text-sm md:text-lg  font-bold text-slate-900">
                                                {{ number_format($product->mmk_discount_price) }} {{ __('messages.mmk') }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-red-600">
                                            {{ $product->discount }}% {{ __('messages.off') }}
                                        </div>
                                        @else
                                        <span class="text-lg font-bold text-slate-900">
                                            {{ number_format($product->mmk_price) }} {{ __('messages.mmk') }}
                                        </span>
                                        @endif
                                    </div>
                                
                                <div class="text-xs text-gray-500 mb-3 gap-2  flex flex-wrap  flex-col md:flex-row justify-start md:justify-between items-start md:items-center ">
                                    <span>  {{ __('messages.moq') }}: {{ $product->MOQ }} </span>
                                    @if($product->sku)
                                    <span>{{ __('messages.sku') }}: {{ $product->sku }}</span>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="addToCart({{ $product->id }},null,{{  $product->MOQ }})"
                                        wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $product->id }},null,{{  $product->MOQ }})"
                                        @if($product->quantity <= 0) disabled @endif
                                        class="flex-1 bg-slate-900 text-white py-2 px-4 rounded-lg hover:bg-slate-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed text-xs 2xl:text-sm "
                                    >
                                        <span wire:loading.remove wire:target="addToCart({{ $product->id }},null,{{  $product->MOQ }})"> {{ __('messages.add_to_cart') }} </span>
                                        <x-filament::loading-indicator wire:loading wire:target="addToCart({{ $product->id }},null,{{  $product->MOQ }})" class="h-4 w-4" />
                                    </button>
                                    <a 
                                        href="{{ route('product', $product->id) }}"
                                        class="bg-gray-100 text-gray-700 py-2 px-4 hidden md:block rounded-lg hover:bg-gray-200 transition-colors text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Variants Grid -->
                @if($showVariants && $variants->count() > 0)
                <div class="mb-8" >
                    <h3 class="text-lg font-semibold mb-4 ">
                        {{ __('messages.wholesale.variants') }} ({{ $variants->count() }})
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-6">
                        @foreach($variants as $variant)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300" wire:key="variant-{{ $variant->id }}">
                            <div class="relative">
                                <img
                                    src="{{ $variant->getFirstMediaUrl('images', 'thumb') ?: $variant->product->productImgThumb ?: asset('images/placeholder.jpg') }}"
                                    data-src="{{ $variant->getFirstMediaUrl('images') ?: $variant->product->productImg ?: asset('images/placeholder.jpg') }}"
                                    alt="{{ $variant->name }}"
                                    class="w-full h-32 md:h-48 lg:52 2xl:h-60 object-cover lazyload blur-sm"
                                    loading="lazy"
                                >
                                <div class="absolute top-2 right-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ __('messages.wholesale.variant') }}
                                    </span>
                                </div>
                                @if($variant->quantity <= 0)
                                <div class="absolute inset-0 bg-black/30 backdrop-blur-xs flex items-center justify-center">
                                    <span class="text-white font-semibold ">
                                        {{ __('messages.out_of_stock') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="p-2 md:p-4">
                                <h4 class="text-sm md:text-lg font-semibold text-gray-900 mb-1 ">
                                    {{ $variant->name }}
                                </h4>
                                <p class="text-sm text-gray-600 mb-2 ">
                                    {{ $variant->product->name }}
                                </p>
                                
                                <div class="text-xs md:text-sm text-gray-600 mb-2 ">
                                    <span>{{ $variant->product->category->name }}</span>
                                    @if($variant->product->brand)
                                    <span class="mx-1">•</span>
                                    <span>{{ $variant->product->brand->name }}</span>
                                    @endif
                                </div>
                                
                               <div class="mb-2">
                                        @if($variant->discount > 0)
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs line-through  font-bold text-slate-300">
                                                {{ number_format($variant->mmk_price) }} <span class="text-xs hidden md:block">{{ __('messages.mmk') }}</span>
                                            </span>
                                            <span class="text-sm md:text-lg  font-bold text-slate-900">
                                                {{ number_format($variant->mmk_discount_price) }} {{ __('messages.mmk') }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-red-600">
                                            {{ $variant->discount }}% {{ __('messages.off') }}
                                        </div>
                                        @else
                                        <span class="text-lg font-bold text-slate-900">
                                            {{ number_format($variant->mmk_price) }} {{ __('messages.mmk') }}
                                        </span>
                                        @endif
                                    </div>
                                
                                 <div class="text-xs text-gray-500 mb-3 gap-2  flex flex-wrap  flex-col md:flex-row justify-start md:justify-between items-start md:items-center ">
                                    <span>  {{ __('messages.moq') }}: {{ $product->MOQ }} </span>
                                    @if($product->sku)
                                    <span>{{ __('messages.sku') }}: {{ $product->sku }}</span>
                                    @endif
                                </div>
                                
                                @if($variant->attributeValues)
                                <div class="text-xs text-gray-600 mb-3 flex flex-wrap gap-1">
                                    @foreach($variant->attributeValues as $key => $value)
                                        <span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1 mb-1">
                                            {{ $value['attribute'] }}: 
                                             @foreach ($value['value'] as $val)
                                                <span class="text-gray-700">{{ $val }}</span>
                                             @endforeach
                                        </span>
                                    @endforeach
                                </div>
                                @endif
                                
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="addVariantToCart({{ $variant->id }}, '{{ $variant->MOQ }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="addVariantToCart({{ $variant->id }}, '{{ $variant->MOQ }}')"
                                        @if($variant->quantity <= 0) disabled @endif
                                        class="flex-1 bg-slate-900 text-white py-2 px-4 rounded-lg hover:bg-slate-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed text-xs 2xl:text-sm "
                                    >
                                        <span wire:loading.remove wire:target="addVariantToCart({{ $variant->id }}, '{{ $variant->MOQ }}')">
                                            {{ __('messages.add_to_cart') }}
                                        </span>
                                        <x-filament::loading-indicator wire:loading wire:target="addVariantToCart({{ $variant->id }}, '{{ $variant->MOQ }}')" class="h-4 w-4" />
                                    </button>
                                    <a 
                                        href="{{ route('product', $variant->product->id) }}"
                                        class="bg-gray-100 text-gray-700 py-2 px-4 hidden md:block rounded-lg hover:bg-gray-200 transition-colors text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Load More Button -->
                @if($hasMorePages)
                <div class="text-center">
                    <button 
                        wire:click="loadMore"
                        class="bg-slate-900 text-white py-3 px-8 rounded-lg hover:bg-slate-800 transition-colors "
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50"
                    >
                        <span wire:loading.remove wire:target="loadMore">{{ __('messages.load_more') }}</span>
                        <span wire:loading wire:target="loadMore">{{ __('messages.loading') }}...</span>
                    </button>
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2 ">
                        {{ __('messages.wholesale.no_products') }}
                    </h3>
                    <p class="text-gray-600 ">
                        {{ __('messages.wholesale.try_different_filters') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="search,setCategory,setBrand,setSortBy,clearAllFilters,clearPriceFilter,toggleAvailableOnly,toggleShowVariants" 
         class="fixed inset-0 bg-black opacity-55 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="">{{ __('messages.loading') }}...</span>
            </div>
        </div>
    </div>
</div>
