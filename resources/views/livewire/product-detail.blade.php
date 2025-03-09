<div class="container mx-auto lg:mt-10 p-6 lg:p-0 ">
    <div x-data="productDetail({{ json_encode($product) }}, {{ json_encode($product->variants) }})" class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Image Gallery -->
        <div class="space-y-4">
            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg relative lg:p-8 ">
                <!-- Discount Tag -->
                <template x-if="selectedVariant?.discount > 0 ">
                    {{-- <div class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-md z-10">
                        <span ></span>% OFF
                    </div> --}}
                    <div class="absolute top-5 right-5 z-10">
                        <div class="bg-red-500 text-white px-3 py-1 rounded-full shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                            <span class="font-bold {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}" x-text="selectedVariant?.discount">
                                %
                            </span>
                            <span class="text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.off') }}
                            </span>
                        </div>
                </template>

                @if($product->discount > 0 )
                <template>
                    {{-- <div class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-md z-10">
                        <span x-text="{{ $product->discount }}"></span>% OFF
                    </div> --}}
                    <div class="absolute top-0 right-0 z-10">
                        <div class="bg-red-500 text-white px-3 py-1 rounded-full shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                            <span class="font-bold {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ $product->has_variant ? $product->variants()->first()->discount : $product->discount }}%
                            </span>
                            <span class="text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.off') }}
                            </span>
                        </div>
                    </div>
                </template>
                @endif

                <img :src="selectedVariant ? selectedVariant.product_img : currentImage"
                     class="h-full w-full object-cover object-center">

            </div>
            @php
                $images = collect();
                if ($product->has_variant) {
                    foreach ($product->variants as $variant) {
                        $images = $images->merge($variant->getMedia('images'));
                    }
                }
                $images = $images->merge($product->getMedia('images'));
            @endphp
            @if($images->count() > 0)
                <div  class="flex justify-center items-center gap-4 ">
                    @foreach($images as $media)
                    <img src="{{ $media->getUrl() }}"  @click="currentImage = '{{ $media->getUrl() }}'; if(!selectedVariant) { selectedVariant = null; }else { selectedVariant.product_img = '{{ $media->getUrl() }}';}" :class="currentImage == '{{ $media->getUrl() }}' ? 'ring-2 ring-slate-500' : '' "  class="h-20 w-20 border p-2  rounded-md object-cover cursor-pointer hover:ring-2 hover:ring-slate-500">
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="space-y-4">
            <div class="flex justify-between items-center ">
                <div class="">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>

                    <!-- Category -->
                    <div class=" ">
                        <div class="text-xs mb-2 text-gray-500">
                            {{ $product->brand?->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $product->category?->name }}
                        </div>
                    </div>

                </div>

                 <!-- Pricing -->
            <div class="mt-4 space-y-2">
                <div class="flex items-baseline gap-2">

                    @if($product->has_variant)
                        <p class="text-2xl font-bold text-gray-900" x-text="getSelectedPrice().toLocaleString() + ' Ks'"></p>
                        <template  x-if="selectedVariant?.discount > 0" >
                            <span class="text-sm text-gray-500 line-through" x-show="selectedVariant" x-text="selectedVariant?.mmk_price.toLocaleString() + 'ks' "></span> Ks
                        </template>
                    @else
                        <p class="text-2xl font-bold text-gray-900" >
                            {{ $product->discount > 0 ? $product->mmk_discount_price : $product->mmk_price }} Ks
                        </p>
                    @endif

                    @if($product->discount > 0 )
                         <p class="text-sm text-gray-500 line-through" > {{ $product->mmk_price }} Ks</p>
                    @endif

                </div>
                <template x-if="selectedVariant?.discount > 0 ">
                    <p class="text-red-600 text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.product_detail.save') }} <span x-text="selectedVariant?.discount"></span>%
                    </p>
                </template>
                @if($product->discount > 0 )
                <template x-if=" {{ $product->discount > 0 }}">
                    <p class="text-red-600 text-sm">
                        Save <span >{{ $product->discount }}</span>%
                    </p>
                </template>
                @endif
           </div>

            </div>
            <!-- Variants Selection -->
            @if($product->has_variant)
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.product_detail.available_variants') }}
                    </h3>
                    <div class="flex items-center  gap-2">
                        @foreach($product->variants as $variant)
                            <button @click="
                                selectedVariant = {{ json_encode($variant) }};
                                console.log(selectedVariant);
                                currentImage = selectedVariant.product_img;"
                                :class="{
                                    'ring-1 bg-slate-200  ring-slate-500': selectedVariant?.id === {{ $variant->id }},
                                    'bg-slate-50': selectedVariant?.id === {{ $variant->id }}
                                }"
                                class="px-4 py-1.5 text-sm border rounded-md  hover:border-slate-400 focus:outline-none">
                                <div class="space-y-1">
                                    <div class="font-medium">{{ $variant->name }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic Variant Attributes -->
                <template x-if="selectedVariant?.attribute_values">
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.product_detail.specifications') }}
                        </h3>
                        <dl class="space-y-2">
                            <template x-for="(attribute, index) in selectedVariant.attribute_values" :key="index">
                                <div class="flex gap-4">
                                    {{-- <dt class="text-sm font-medium text-gray-500" x-text="attribute.attribute"></dt> --}}
                                    <template x-for="(value, vIndex) in attribute.value" :key="vIndex">
                                        <dd class="text-sm inline border rounded px-2 bg-slate-50 text-gray-900" x-text="value"></dd>
                                    </template>
                                </div>
                            </template>
                        </dl>
                    </div>
                </template>

                {{-- <!-- Variant Attributes -->
                @if($variant->attributeValues)
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-900">Specifications</h3>
                        <dl class="space-y-2">
                            @foreach($variant->attributeValues as $attributeValue)
                                <div class="flex gap-4">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ $attributeValue['attribute'] }}
                                    </dt>
                                    @foreach ($attributeValue['value'] as $value)
                                    <dd class="text-sm inline  border rounded px-2 bg-slate-50  text-gray-900 col-span-2">
                                        {{ $value }}
                                    </dd>
                                    @endforeach

                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif --}}
            @else
                <!-- Product Attributes -->
                @if($product->attributeValues)
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-900">Specifications</h3>
                        <dl class="space-y-2">
                            @foreach($product->attributeValues as $attributeValue)
                                <div class="flex gap-4">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ $attributeValue['attribute'] }}
                                    </dt>
                                    @foreach ($attributeValue['value'] as $value)
                                        <dd class="text-sm inline  border rounded px-2 bg-slate-50  text-gray-900 col-span-2">
                                            {{ $value }}
                                        </dd>
                                    @endforeach
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            @endif



            <!-- MOQ -->
            <div class="text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.product_detail.min_order') }}: {{ $product->MOQ }} {{ __('messages.product_detail.pieces') }}
            </div>

            <!-- Quantity Selector and Add to Cart -->
            <div class="flex items-center gap-4">
                <div class="flex items-center border rounded-md">
                    <button @click="quantity > 1 ? quantity-- : null"
                            class="px-3 py-2 border-r hover:bg-gray-100">
                        -
                    </button>
                    <input type="text" x-model="quantity" min="1"
                           class="w-16 text-center border-none focus:ring-0"
                           :min="{{ $product->MOQ }}">
                    <button @click="quantity++"
                            class="px-3 py-2 border-l hover:bg-gray-100">
                        +
                    </button>
                </div>
                <button
                    @click="addToCart"  wire:loading.attr="disabled"
                    class="w-full bg-slate-900 text-white py-2 px-4 rounded hover:bg-slate-800 transition-colors {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.product_detail.add_to_cart') }}
                </button>

            </div>

            <div class="mt-10 font-thin font-jost text-justify" style="line-height: 2rem;"> {!! $product->description !!} </div>

        </div>
    </div>

    <!-- Related Products Section -->
    @if($this->relatedProducts->isNotEmpty())
        <div class="mt-16 mb-10">
            <h2 class="text-2xl font-bold text-gray-900 font-bebas  mb-6 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.product_detail.related_products') }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($this->relatedProducts as $product)
                    <x-product-item :$product/>
                @endforeach
            </div>
        </div>
        @else
        <div class="flex items-center justify-center py-12">
            <div class="text-center space-y-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.product_detail.no_related_products') }}
                </h3>
                <p class="text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.product_detail.no_related_description') }}
                </p>
            </div>
        </div>
    @endif
</div>
<script>

    document.addEventListener('alpine:init', () => {
        Alpine.data('productDetail', (product, variants) => ({
            currentImage: product.productImg,
            selectedVariant: variants.length > 0 ? variants[0] : null,
            quantity: 1,

            // init() {
            //     if (typeof this.selectedVariant === 'string') {
            //         this.selectedVariant = JSON.parse(this.selectedVariant);
            //         console.log(this.selectedVariant);
            //     }
            // },

            addToCart() {
                // Call Livewire method with selectedVariant
                this.$wire.addToCart(
                    product.id,
                    this.selectedVariant ? this.selectedVariant.id : null,
                    this.quantity
                )
            },

            getSelectedPrice() {
                if (this.selectedVariant != null) {
                    return this.selectedVariant.discount > 0
                        ? parseFloat(this.selectedVariant.mmk_discount_price)
                        : parseFloat(this.selectedVariant.mmk_price);
                }
                return parseFloat(product.discount > 0
                    ? product.mmk_discount_price
                    : product.mmk_price);
            },

            formatPrice(price) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(price || 0);
            }
        }))
    })


</script>
