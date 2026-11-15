@php
    $hasDiscount = ! $product->has_variant && $product->discount > 0;
    $minimumQuantity = max(1, (int) $product->MOQ);
@endphp

<div class="shop-container py-8 md:py-12">

    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-xs text-muted" aria-label="Breadcrumb">
        <a href="{{ route('welcome') }}" wire:navigate class="hover:text-ink">{{ __('messages.home') }}</a>
        <x-heroicon-m-chevron-right class="size-3" />
        <a href="{{ route('products') }}" wire:navigate class="hover:text-ink">{{ __('messages.navigation.products') }}</a>
        <x-heroicon-m-chevron-right class="size-3" />
        <span class="truncate text-ink-soft">{{ $product->name }}</span>
    </nav>

    <div x-data="productDetail(@js($product), @js($product->variants), {{ $minimumQuantity }})"
        class="grid gap-10 lg:grid-cols-12 lg:gap-16">

        {{-- Gallery --}}
        <div class="space-y-4 lg:col-span-7">
            <div class="relative aspect-square overflow-hidden rounded-3xl bg-accent-soft/50">
                <img :src="activeImage" alt="{{ $product->name }}" class="size-full object-cover" />

                <div class="absolute left-4 top-4 flex gap-2">
                    @if ($product->has_variant)
                        <template x-if="selectedVariant?.discount > 0">
                            <x-shop.badge tone="sale"><span x-text="'−' + selectedVariant.discount + '%'"></span></x-shop.badge>
                        </template>
                    @elseif ($hasDiscount)
                        <x-shop.badge tone="sale">−{{ $product->discount }}%</x-shop.badge>
                    @endif
                </div>
            </div>

            @if ($this->galleryImages->count() > 1)
                <div class="hide-scrollbar flex gap-3 overflow-x-auto pb-1">
                    @foreach ($this->galleryImages as $image)
                        <button type="button" wire:key="gallery-{{ $loop->index }}"
                            @click="activeImage = @js($image['full'])"
                            class="size-20 shrink-0 overflow-hidden rounded-xl border-2 bg-accent-soft/50 transition-colors md:size-24"
                            :class="activeImage === @js($image['full']) ? 'border-ink' : 'border-transparent hover:border-line'">
                            <img src="{{ $image['thumb'] }}" alt="" loading="lazy" class="size-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="lg:col-span-5">
            <div class="space-y-8 lg:sticky lg:top-28">

                <header class="space-y-3">
                    @if ($product->brand)
                        <p class="text-[11px] font-medium uppercase tracking-[0.25em] text-accent">{{ $product->brand->name }}</p>
                    @endif
                    <h1 class="font-display text-4xl font-medium leading-tight text-ink md:text-5xl">{{ $product->name }}</h1>
                    @if ($product->category)
                        <p class="text-sm text-ink-soft">{{ $product->category->name }}</p>
                    @endif
                </header>

                {{-- Price --}}
                <div class="border-y border-line py-6">
                    @if ($product->has_variant)
                        <div class="flex flex-wrap items-baseline gap-x-3">
                            <span class="text-2xl font-medium tabular-nums md:text-3xl"
                                :class="selectedVariant?.discount > 0 ? 'text-sale' : 'text-ink'">
                                <span x-text="formatPrice(selectedPrice())"></span>
                                <span class="text-[0.6em] font-normal text-ink-soft">{{ __('messages.mmk') }}</span>
                            </span>
                            <template x-if="selectedVariant?.discount > 0">
                                <span class="text-sm text-muted line-through tabular-nums" x-text="formatPrice(selectedVariant.mmk_price)"></span>
                            </template>
                        </div>
                    @else
                        <x-shop.price size="lg" :amount="$hasDiscount ? $product->mmk_discount_price : $product->mmk_price"
                            :original="$hasDiscount ? $product->mmk_price : null" />
                        @if ($product->quantity > 0)
                            <p class="mt-2 text-xs text-ink-soft">{{ $product->quantity }} {{ __('messages.in_stock') }}</p>
                        @endif
                    @endif

                    <ul class="mt-5 space-y-2 text-sm text-ink-soft">
                        <li class="flex items-center gap-3">
                            @if ($product->is_shipped)
                                <x-heroicon-o-truck class="size-5 text-accent" />
                                {{ __('messages.product_detail.free_shipping') }}
                            @else
                                <x-heroicon-o-building-storefront class="size-5 text-accent" />
                                {{ __('messages.product_detail.pickup_only') }}
                            @endif
                        </li>
                        <li class="flex items-center gap-3">
                            @if ($product->is_return)
                                <x-heroicon-o-arrow-path class="size-5 text-accent" />
                                {{ __('messages.product_detail.returns_accepted') }}
                            @else
                                <x-heroicon-o-no-symbol class="size-5 text-accent" />
                                {{ __('messages.product_detail.no_returns') }}
                            @endif
                        </li>
                    </ul>
                </div>

                {{-- Variants & specifications --}}
                @if ($product->has_variant)
                    <div>
                        <x-shop.label>{{ __('messages.product_detail.available_variants') }}</x-shop.label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->variants as $variant)
                                <button type="button" wire:key="variant-{{ $variant->id }}"
                                    @click="selectVariant(@js($variant))"
                                    class="rounded-full border px-4 py-2 text-sm transition-colors"
                                    :class="selectedVariant?.id === {{ $variant->id }} ? 'border-ink bg-ink text-canvas' : 'border-line bg-surface text-ink-soft hover:border-ink/40 hover:text-ink'">
                                    {{ $variant->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <template x-if="selectedVariant?.attribute_values?.length">
                        <div>
                            <x-shop.label>{{ __('messages.product_detail.specifications') }}</x-shop.label>
                            <dl class="divide-y divide-line border-y border-line text-sm">
                                <template x-for="(attribute, index) in selectedVariant.attribute_values" :key="index">
                                    <div class="flex justify-between gap-4 py-2.5">
                                        <dt class="text-muted" x-text="attribute.attribute"></dt>
                                        <dd class="text-end text-ink" x-text="[].concat(attribute.value).join(', ')"></dd>
                                    </div>
                                </template>
                            </dl>
                        </div>
                    </template>
                @elseif (! empty($product->attributeValues))
                    <div>
                        <x-shop.label>{{ __('messages.product_detail.specifications') }}</x-shop.label>
                        <dl class="divide-y divide-line border-y border-line text-sm">
                            @foreach ($product->attributeValues as $attributeValue)
                                <div class="flex justify-between gap-4 py-2.5">
                                    <dt class="text-muted">{{ $attributeValue['attribute'] }}</dt>
                                    <dd class="text-end text-ink">{{ implode(', ', (array) $attributeValue['value']) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                {{-- Quantity & add to cart --}}
                <div class="space-y-3">
                    <p class="text-xs text-muted">
                        {{ __('messages.product_detail.min_order') }}: {{ $product->MOQ }} {{ __('messages.product_detail.pieces') }}
                    </p>

                    <div class="flex gap-3">
                        <div class="flex h-12 items-center rounded-full border border-line bg-surface">
                            <button type="button" @click="decrement()" class="flex size-12 items-center justify-center text-ink-soft hover:text-ink disabled:opacity-30"
                                :disabled="quantity <= minimumQuantity" aria-label="−">
                                <x-heroicon-m-minus class="size-4" />
                            </button>
                            <input type="number" x-model.number="quantity" :min="minimumQuantity"
                                class="w-10 border-0 bg-transparent p-0 text-center text-sm tabular-nums text-ink focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" @click="increment()" class="flex size-12 items-center justify-center text-ink-soft hover:text-ink" aria-label="+">
                                <x-heroicon-m-plus class="size-4" />
                            </button>
                        </div>

                        <x-shop.button size="lg" class="flex-1" x-on:click="addToCart()" wire:loading.attr="disabled" wire:target="addToCart">
                            <x-heroicon-o-shopping-bag wire:loading.remove wire:target="addToCart" class="size-5" />
                            <x-filament::loading-indicator wire:loading wire:target="addToCart" class="size-5" />
                            {{ __('messages.product_detail.add_to_cart') }}
                        </x-shop.button>
                    </div>
                </div>

                {{-- Description --}}
                @if ($product->description)
                    <div class="border-t border-line pt-8 text-sm leading-7 text-ink-soft [&_a]:text-accent-dark [&_a]:underline [&_li]:ms-5 [&_ol]:list-decimal [&_p]:mb-4 [&_ul]:list-disc">
                        {!! $product->description !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    <section class="mt-24 border-t border-line pt-16">
        <livewire:product-reviews :product="$product" />
    </section>

    {{-- Related products --}}
    <section class="mt-24">
        @if ($this->relatedProducts->isNotEmpty())
            <x-shop.section-heading :title="__('messages.product_detail.related_products')" />
            <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-5 lg:gap-x-6">
                @foreach ($this->relatedProducts as $relatedProduct)
                    <x-shop.product-card :product="$relatedProduct" type="related" />
                @endforeach
            </div>
        @else
            <x-shop.empty-state icon="heroicon-o-squares-2x2"
                :title="__('messages.product_detail.no_related_products')"
                :description="__('messages.product_detail.no_related_description')" />
        @endif
    </section>

    <script>
        function productDetail(product, variants, minimumQuantity) {
            const firstVariant = variants.length > 0 ? variants[0] : null;

            return {
                selectedVariant: firstVariant,
                activeImage: firstVariant?.product_img || product.productImg,
                minimumQuantity: minimumQuantity,
                quantity: minimumQuantity,

                selectVariant(variant) {
                    this.selectedVariant = variant;
                    this.activeImage = variant.product_img || product.productImg;
                },

                increment() {
                    this.quantity = Number(this.quantity) + 1;
                },

                decrement() {
                    this.quantity = Math.max(this.minimumQuantity, Number(this.quantity) - 1);
                },

                selectedPrice() {
                    const source = this.selectedVariant ?? product;

                    return parseFloat(source.discount > 0 ? source.mmk_discount_price : source.mmk_price);
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(price || 0);
                },

                addToCart() {
                    this.$wire.addToCart(product.id, this.selectedVariant?.id ?? null, this.quantity);
                },
            };
        }
    </script>
</div>
