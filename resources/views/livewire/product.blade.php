<div class="shop-container space-y-20 py-12 md:space-y-28 md:py-20">

    {{-- Recommended --}}
    <section>
        <x-shop.section-heading :title="__('messages.recommended_products')" />

        @if ($this->recommendedProducts->isNotEmpty())
            <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-6">
                @foreach ($this->recommendedProducts as $recommendedProduct)
                    <x-shop.product-card :product="$recommendedProduct" type="recommended" />
                @endforeach
            </div>
        @else
            <p class="text-sm text-ink-soft">{{ __('messages.no_recommended_products') }}</p>
        @endif
    </section>

    {{-- Wholesale --}}
    @if ($this->wholesaleProducts->isNotEmpty())
        <section>
            <x-shop.section-heading :title="__('messages.wholesale_products')" :href="route('wholesale')" />

            <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-6">
                @foreach ($this->wholesaleProducts as $wholesaleProduct)
                    <x-shop.product-card :product="$wholesaleProduct" type="wholesale" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Browse by category --}}
    <section>
        <x-shop.section-heading
            :title="$selectedCategory ? $this->categories->find($selectedCategory)?->name : __('messages.latest_products')"
            :href="route('products')" />

        <x-shop.chip-rail class="mb-10">
            <x-shop.chip wire:key="category-all" wire:click="filterByCategory(null)" :active="is_null($selectedCategory)">
                {{ __('messages.all_categories') }}
            </x-shop.chip>

            @foreach ($this->categories as $category)
                <x-shop.chip wire:key="category-{{ $category->id }}"
                    wire:click="filterByCategory({{ $category->id }})"
                    :active="$selectedCategory == $category->id"
                    :count="$category->products_count">
                    {{ $category->name }}
                </x-shop.chip>
            @endforeach
        </x-shop.chip-rail>

        <div wire:loading.flex wire:target="filterByCategory" class="hidden items-center justify-center gap-2 py-6 text-sm text-ink-soft">
            <x-filament::loading-indicator class="size-5" />
            {{ __('messages.loading_products') }}
        </div>

        <div wire:loading.remove wire:target="filterByCategory">
            @if ($this->products->isNotEmpty())
                <div wire:key="products-{{ $selectedCategory ?? 'all' }}" class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-6">
                    @foreach ($this->products as $product)
                        <x-shop.product-card :product="$product" type="latest" />
                    @endforeach
                </div>
            @else
                <x-shop.empty-state icon="heroicon-o-squares-2x2" :title="__('messages.no_products')" />
            @endif
        </div>

        @if ($hasMorePages)
            <div x-intersect.margin.200px="$wire.loadMore()" wire:key="load-more" class="flex justify-center py-10">
                <x-filament::loading-indicator wire:loading wire:target="loadMore" class="size-5 text-accent" />
            </div>
        @endif
    </section>
</div>
