<div class="shop-container py-10 md:py-16">
    <x-shop.section-heading :title="__('messages.navigation.products')" as="h1" class="mb-8!" />

    {{-- Search & discount toggle --}}
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <x-heroicon-o-magnifying-glass class="pointer-events-none absolute start-4 top-1/2 z-20 size-4 -translate-y-1/2 text-muted" />
            <x-text-input wire:model.live.debounce.500ms="search" class="rounded-full! ps-11! pe-11!"
                :placeholder="__('messages.search_product')" />
            <x-filament::loading-indicator wire:loading wire:target="search" class="absolute end-4 top-1/2 z-20 size-4 -translate-y-1/2 text-accent" />
        </div>

        <x-shop.chip wire:click="toggleDiscountFilter" :active="$discountOnly" class="justify-center py-2.5">
            <x-heroicon-o-tag class="size-4" />
            {{ __('messages.discount_only') }}
        </x-shop.chip>
    </div>

    {{-- Filter type tabs --}}
    <div class="mb-5 flex gap-8 border-b border-line">
        @foreach (['category' => __('messages.categories'), 'brand' => __('messages.brands')] as $type => $label)
            <button type="button" x-on:click="$wire.filterType = '{{ $type }}'"
                class="-mb-px border-b pb-3 text-[11px] font-medium uppercase tracking-[0.2em] transition-colors"
                :class="$wire.filterType === '{{ $type }}' ? 'border-ink text-ink' : 'border-transparent text-muted hover:text-ink'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Category chips --}}
    <div wire:show="filterType === 'category'" class="mb-10">
        <x-shop.chip-rail>
            <x-shop.chip wire:click="setCategory(null)" :active="is_null($selectedCategory)">{{ __('messages.all_categories') }}</x-shop.chip>
            @foreach ($categories as $category)
                <x-shop.chip wire:key="category-{{ $category->id }}" wire:click="setCategory({{ $category->id }})" :active="$selectedCategory === $category->id">
                    {{ $category->name }}
                </x-shop.chip>
            @endforeach
        </x-shop.chip-rail>
    </div>

    {{-- Brand chips --}}
    <div wire:show="filterType === 'brand'" class="mb-10">
        <x-shop.chip-rail>
            <x-shop.chip wire:click="setBrand(null)" :active="is_null($selectedBrand)">{{ __('messages.all_brands') }}</x-shop.chip>
            @foreach ($brands as $brand)
                <x-shop.chip wire:key="brand-{{ $brand->id }}" wire:click="setBrand({{ $brand->id }})" :active="$selectedBrand === $brand->id">
                    {{ $brand->name }}
                </x-shop.chip>
            @endforeach
        </x-shop.chip-rail>
    </div>

    <div wire:loading.flex wire:target="setCategory,setBrand,toggleDiscountFilter" class="hidden items-center justify-center gap-2 py-6 text-sm text-ink-soft">
        <x-filament::loading-indicator class="size-5" />
        {{ __('messages.loading_products') }}
    </div>

    {{-- Results --}}
    @if ($products->isEmpty())
        <x-shop.empty-state icon="heroicon-o-magnifying-glass"
            :title="__('messages.no_products')"
            :description="__('messages.search_no_results')" />
    @else
        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-6">
            @foreach ($products as $product)
                <x-shop.product-card :product="$product" />
            @endforeach
        </div>

        @if ($hasMorePages)
            <div class="mt-14 flex justify-center">
                <x-shop.button variant="secondary" size="lg" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore">
                    <span wire:loading.remove wire:target="loadMore">{{ __('messages.load_more') }}</span>
                    <span wire:loading wire:target="loadMore">{{ __('messages.loading_products') }}</span>
                </x-shop.button>
            </div>
        @endif
    @endif
</div>
