@php
    $hasActiveFilters = $search || $selectedCategory || $selectedBrand || $minPrice || $maxPrice || $availableOnly;
    $visibleVariants = $showVariants ? $variants : collect();
    $sortOptions = [
        'name' => __('messages.name'),
        'price' => __('messages.price'),
        'created_at' => __('messages.newest'),
    ];
@endphp

<div class="shop-container py-10 md:py-16" x-data="{ showFilters: false }">

    <div class="mb-10 flex items-end justify-between gap-4">
        <x-shop.section-heading as="h1" class="mb-0!"
            :eyebrow="__('messages.wholesale.wholesale')"
            :title="__('messages.wholesale.title', ['count' => $totalProducts])" />

        <x-shop.button variant="secondary" size="sm" class="lg:hidden" x-on:click="showFilters = ! showFilters">
            <x-heroicon-o-adjustments-horizontal class="size-4" />
            {{ __('messages.filters') }}
        </x-shop.button>
    </div>

    <div class="grid gap-10 lg:grid-cols-[17rem_1fr] lg:gap-12">

        {{-- Filters --}}
        <aside class="hidden lg:block" :class="showFilters && 'block!'">
            <div class="lg:sticky lg:top-28">
                <div class="flex items-center justify-between pb-5">
                    <h2 class="font-display text-2xl text-ink">{{ __('messages.filters') }}</h2>
                    @if ($hasActiveFilters)
                        <button type="button" wire:click="clearAllFilters" class="text-xs text-ink-soft underline underline-offset-4 hover:text-ink">
                            {{ __('messages.clear_all') }}
                        </button>
                    @endif
                </div>

                <div class="relative pb-5">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute start-4 top-3 z-20 size-4 text-muted" />
                    <x-text-input wire:model.live.debounce.500ms="search" class="rounded-full! ps-11!"
                        :placeholder="__('messages.search_wholesale')" />
                </div>

                <x-shop.filter-group :title="__('messages.price_range')">
                    <div class="grid grid-cols-2 gap-2">
                        <x-text-input wire:model.live.debounce.500ms="minPrice" type="number" min="0" :placeholder="__('messages.min_price')" />
                        <x-text-input wire:model.live.debounce.500ms="maxPrice" type="number" min="0" :placeholder="__('messages.max_price')" />
                    </div>
                    @if ($minPrice || $maxPrice)
                        <button type="button" wire:click="clearPriceFilter" class="mt-3 text-xs text-ink-soft underline underline-offset-4 hover:text-ink">
                            {{ __('messages.clear_price_filter') }}
                        </button>
                    @endif
                </x-shop.filter-group>

                @foreach ([
                    ['title' => __('messages.categories'), 'items' => $categories, 'model' => 'selectedCategory', 'selected' => $selectedCategory, 'clear' => __('messages.clear_category')],
                    ['title' => __('messages.brands'), 'items' => $brands, 'model' => 'selectedBrand', 'selected' => $selectedBrand, 'clear' => __('messages.clear_brand')],
                ] as $group)
                    @if ($group['items']->isNotEmpty())
                        <x-shop.filter-group :title="$group['title']">
                            <div class="space-y-1">
                                @foreach ($group['items'] as $option)
                                    <label wire:key="{{ $group['model'] }}-{{ $option->id }}"
                                        class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-2 py-1.5 text-sm transition-colors hover:bg-surface has-[:checked]:text-ink text-ink-soft">
                                        <span class="flex items-center gap-3">
                                            <input type="radio" name="{{ $group['model'] }}" value="{{ $option->id }}" wire:model.live="{{ $group['model'] }}"
                                                class="size-4 accent-ink">
                                            {{ $option->name }}
                                        </span>
                                        <span class="text-xs tabular-nums text-muted">{{ $option->products_count }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if ($group['selected'])
                                <button type="button" wire:click="$set('{{ $group['model'] }}', null)" class="mt-3 text-xs text-ink-soft underline underline-offset-4 hover:text-ink">
                                    {{ $group['clear'] }}
                                </button>
                            @endif
                        </x-shop.filter-group>
                    @endif
                @endforeach

                <div class="space-y-3 border-t border-line pt-5 text-sm text-ink-soft">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" wire:click="toggleAvailableOnly" @checked($availableOnly)
                            class="size-4 accent-ink">
                        {{ __('messages.available_only') }}
                    </label>
                    <label class="flex cursor-pointer items-center gap-3">
                        <input type="checkbox" wire:click="toggleShowVariants" @checked($showVariants)
                            class="size-4 accent-ink">
                        {{ __('messages.show_variants') }}
                    </label>
                </div>
            </div>
        </aside>

        {{-- Results --}}
        <div class="relative min-w-0">
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-line pb-5">
                <div class="hide-scrollbar flex items-center gap-2 overflow-x-auto">
                    <span class="me-1 text-[11px] uppercase tracking-[0.2em] text-muted">{{ __('messages.sort_by') }}</span>
                    @foreach ($sortOptions as $field => $label)
                        <x-shop.chip wire:click="setSortBy('{{ $field }}')" :active="$sortBy === $field" class="py-1.5!">
                            {{ $label }}
                            @if ($sortBy === $field)
                                @if ($sortDirection === 'asc')
                                    <x-heroicon-m-arrow-up class="size-3.5" />
                                @else
                                    <x-heroicon-m-arrow-down class="size-3.5" />
                                @endif
                            @endif
                        </x-shop.chip>
                    @endforeach
                </div>

                <p class="hidden text-xs text-ink-soft md:block">
                    {{ __('messages.showing_results', ['count' => $products->count() + $visibleVariants->count()]) }}
                </p>
            </div>

            {{-- Loading veil over the results only --}}
            <div wire:loading.flex
                wire:target="search,minPrice,maxPrice,selectedCategory,selectedBrand,setCategory,setBrand,setSortBy,clearAllFilters,clearPriceFilter,toggleAvailableOnly,toggleShowVariants"
                class="absolute inset-0 top-16 z-10 hidden items-start justify-center bg-canvas/70 pt-24 backdrop-blur-[1px]">
                <x-filament::loading-indicator class="size-6 text-accent" />
            </div>

            @if ($products->isNotEmpty() || $visibleVariants->isNotEmpty())
                @if ($products->isNotEmpty())
                    <section class="mb-16">
                        <h2 class="mb-6 text-[11px] font-medium uppercase tracking-[0.2em] text-ink-soft">
                            {{ __('messages.wholesale.products') }} <span class="text-muted">({{ $products->count() }})</span>
                        </h2>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 xl:grid-cols-4 lg:gap-x-6">
                            @foreach ($products as $product)
                                <x-shop.wholesale-card :item="$product" kind="product" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($visibleVariants->isNotEmpty())
                    <section class="mb-16">
                        <h2 class="mb-6 text-[11px] font-medium uppercase tracking-[0.2em] text-ink-soft">
                            {{ __('messages.wholesale.variants') }} <span class="text-muted">({{ $visibleVariants->count() }})</span>
                        </h2>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 xl:grid-cols-4 lg:gap-x-6">
                            @foreach ($visibleVariants as $variant)
                                <x-shop.wholesale-card :item="$variant" kind="variant" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($hasMorePages)
                    <div class="flex justify-center">
                        <x-shop.button variant="secondary" size="lg" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore">
                            <span wire:loading.remove wire:target="loadMore">{{ __('messages.load_more') }}</span>
                            <span wire:loading wire:target="loadMore">{{ __('messages.loading') }}…</span>
                        </x-shop.button>
                    </div>
                @endif
            @else
                <x-shop.empty-state icon="heroicon-o-archive-box"
                    :title="__('messages.wholesale.no_products')"
                    :description="__('messages.wholesale.try_different_filters')" />
            @endif
        </div>
    </div>
</div>
