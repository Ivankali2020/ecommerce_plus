<div>
    <div class="container mx-auto px-4 py-4 lg:p-8">
        <x-locale-text class="text-xl mb-2  lg:mb-8 lg:text-4xl font-bold">
            {{ __('messages.recommended_products') }}
        </x-locale-text>

        <!-- Recommended Products Section -->
        <div wire:key="recommended-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 lg:gap-4 gap-3">
            @forelse ($this->recommendedProducts as $recProduct)
                <x-product-item wire:ignore :product="$recProduct" type="recommended" />
            @empty
                <div wire:key="no-recommended">{{ __('messages.no_recommended_products') }}</div>
            @endforelse
        </div>

        <div class="lg:mb-4  mt-2 lg:mt-0 ">
            <x-locale-text class="text-xl mb-4  lg:mb-8  lg:text-4xl font-bold lg:mt-8">
                {{ __('messages.categories') }}
            </x-locale-text>

            <div class=" pb-4 overflow-x-auto hide-scrollbar">
                <div class="flex flex-nowrap gap-2  min-w-full">
                    <!-- All Categories Button -->
                    <button wire:key="category-all" 
                            wire:loading.class="opacity-50"
                            wire:click="filterByCategory(null)"
                            class="flex-none px-4 py-1.5 lg:py-2.5 text-sm font-medium rounded-full transition-all duration-200 shadow-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}
                                {{ is_null($selectedCategory)
                                    ? 'bg-slate-900 text-white ring-2 ring-slate-900/20'
                                    : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }}">
                        {{ __('messages.all_categories') }}
                    </button>

                    <!-- Category Buttons -->
                    @foreach($this->categories as $category)
                        <button wire:key="category-{{ $category->id }}"
                                wire:click="filterByCategory({{ $category->id }})"
                                class="flex-none px-4 py-1.5 lg:py-2.5 text-sm font-medium rounded-full transition-all duration-200 shadow-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}
                                    {{ $selectedCategory == $category->id
                                        ? 'bg-slate-900 text-white ring-2 ring-slate-900/20'
                                        : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }}">
                            <span>{{ $category->name }}</span>
                            <span class="ml-1.5 text-xs inline-flex items-center justify-center px-2 py-0.5 rounded-full
                                {{ $selectedCategory == $category->id
                                    ? 'bg-white/20 text-white'
                                    : 'bg-slate-100 text-slate-600' }}">
                                {{ $category->products_count }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="absolute right-0 top-0 h-full w-8 bg-gradient-to-l from-white to-transparent pointer-events-none md:hidden"></div>
        </div>

        <div class="flex justify-between items-center ">
            <x-locale-text class="text-xl lg:text-4xl mb-2  lg:mb-8  font-bold mt-4  lg:mt-8">
                {{ $selectedCategory ? $this->categories->find($selectedCategory)->name : __('messages.latest_products') }}
            </x-locale-text>

            <nav class="flex justify-between hidden lg:block  px-3.5 py-1 ">
                <ol class="inline-flex items-center  space-x-1 text-xs text-neutral-500 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-medium sm:mb-0">
                    <li class="flex items-center h-full">
                        <a href="/" class="py-1 hover:text-neutral-900" title="{{ __('messages.home') }}">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
                            </svg>
                        </a>
                    </li>
                    <svg class="w-5 h-5 text-gray-400/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g fill="none" stroke="none">
                            <path d="M10 8.013l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                    </svg>
                    <li>
                        <a href="/" class="inline-flex items-center py-1 font-normal hover:text-neutral-900 focus:outline-none">
                            {{ __('messages.latest_products') }}
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div wire:loading.flex wire:target="filterByCategory" class="hidden items-center justify-center w-full py-4">
            <x-filament::loading-indicator class="h-5 w-5" />
            <span class="ml-2 text-sm text-slate-500">{{ __('messages.loading_products') }}</span>
        </div>

        <!-- Latest Products Section -->
        <div wire:key="products-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 lg:gap-4 gap-3">
            @forelse ($this->products as $product)
                <x-product-item :product="$product" type="latest" />
            @empty
                <div wire:key="no-products-{{ $selectedCategory ?? 'all' }}">{{ __('messages.no_products') }}</div>
            @endforelse
        </div>

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
