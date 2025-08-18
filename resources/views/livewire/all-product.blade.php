<div class="container mb-20 lg:mb-0 mx-auto px-4 py-4  lg:py-8">
    <h2 class=" text-xl lg:text-3xl  mb-4 lg:mb-10 ">
        {{ __('messages.navigation.products') }}
    </h2>
    <!-- Search Box -->
    <div class=" mb-4 lg:mb-6 flex justify-between gap-4 flex-col lg:flex-row ">
        <div class="relative flex-1 ">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>  
            </div>
            <x-text-input 
                wire:model.live.debounce.500ms="search"
                class="w-full pl-10 pr-4 py-2 rounded-lg "
                placeholder="{{ __('messages.search_product') }}" />
            <div class="absolute inset-y-0 end-0 top-0  flex items-center pr-3 pointer-events-none">
                <x-filament::loading-indicator class="h-5 w-5"  wire:loading />
            </div>
        </div>
        
        <!-- Discount Filter Button -->
        <div class="flex justify-end ">
            <button 
                wire:click="toggleDiscountFilter"
                class="flex items-center px-3 py-2 text-sm h-full font-medium rounded-md {{ $discountOnly ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('messages.discount_only') }}
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-4">
        <div class="flex border-b border-gray-200">
            <button 
               x-on:click="$wire.filterType = 'category'" 
                class="py-2 px-4 font-bold text-sm border-b-2 transition-colors "
                :class="$wire.filterType === 'category' ? 
                    'border-slate-900 text-slate-900' : 
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                {{ __('messages.categories') }}
            </button>
            <button 
              x-on:click="$wire.filterType = 'brand'" 
                class="py-2 px-4 font-bold text-sm border-b-2 transition-colors "
                :class="$wire.filterType === 'brand' ? 
                    'border-slate-900 text-slate-900' : 
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
                {{ __('messages.brands') }}
            </button>
        </div>

    </div>

    <!-- Category Filter -->
    <div class="mb-4 lg:mb-8 overflow-x-auto hide-scrollbar" wire:show="filterType === 'category'">
        <div class="flex space-x-1 lg:space-x-4 pb-2">
            <button 
                wire:click="setCategory(null)" 
                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ is_null($selectedCategory) ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
            >
                {{ __('messages.all_categories') }}
            </button>
            
            @foreach($categories as $category)
                <button 
                    wire:click="setCategory({{ $category->id }})" 
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $selectedCategory === $category->id ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Brand Filter -->
    <div class="mb-4 lg:mb-8 overflow-x-auto hide-scrollbar" wire:show="filterType === 'brand'">
        <div class="flex space-x-1 lg:space-x-4  pb-2">
            <button 
                wire:click="setBrand(null)" 
                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ is_null($selectedBrand) ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
            >
                {{ __('messages.all_brands') }}
            </button>
            
            @foreach($brands as $brand)
                <button 
                    wire:click="setBrand({{ $brand->id }})" 
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $selectedBrand === $brand->id ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
                >
                    {{ $brand->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div wire:loading.flex wire:target='setCategory'  class="hidden items-center justify-center w-full py-4">
        <x-filament::loading-indicator class="h-5 w-5" />
        <span class="ml-2 text-sm text-slate-500">{{ __('messages.loading_products') }}</span>
    </div>

    <!-- Products Grid -->
    @if($products->isEmpty())
        <div class="flex items-center justify-center py-12">
            <div class="text-center space-y-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}">
                    {{ __('messages.no_products') }}
                </h3>
                <p class="text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}">
                    {{ __('messages.search_no_results') }}
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($products as $product)
                <x-product-item :product="$product" />
            @endforeach
        </div>

        <!-- Load More Button -->
        @if($hasMorePages)
            <div class="mt-8 flex justify-center">
                <button 
                    wire:click="loadMore"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}"
                >
                    <span wire:loading.remove wire:target="loadMore">{{ __('messages.load_more') }}</span>
                    <span wire:loading wire:target="loadMore">{{ __('messages.loading_products') }}</span>
                </button>
            </div>
        @endif
    @endif
</div>
