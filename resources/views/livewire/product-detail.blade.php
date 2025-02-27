<template x-teleport="body">
    <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen"
        x-cloak>
        <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modalOpen=false"
            class="absolute inset-0 w-full h-full bg-white backdrop-blur-sm bg-opacity-70">
        </div>

        <div x-show="modalOpen" x-trap.inert.noscroll="modalOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 sm:scale-95"
            class="relative m-4  w-full py-6 bg-white border shadow-lg px-7 border-neutral-200 sm:max-w-4xl sm:rounded-lg">

            <!-- Close Button -->
            <button @click="modalOpen=false"
                class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded-full hover:text-gray-800 hover:bg-gray-50">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div x-data="{ 
                currentImage: '{{ $this->product->productImg }}',
                selectedVariant: {{ $this->product->has_variant ? 
                    json_encode([
                        'id' => $this->product->variants->first()->id,
                        'price' => $this->product->variants->first()->mmk_price,
                        'discount_price' => $this->product->variants->first()->mmk_discount_price,
                        'discount' => $this->product->variants->first()->discount,
                        'image_url' => $this->product->variants->first()->getFirstMediaUrl('images')
                    ]) 
                    : 'null' 
                }},
                quantity: 1,
                getSelectedPrice() {
                    if (this.selectedVariant) {
                        return this.selectedVariant.discount > 0 
                            ? parseFloat(this.selectedVariant.discount_price) 
                            : parseFloat(this.selectedVariant.price);
                    }
                    return parseFloat({{ $this->product->discount > 0 
                        ? $this->product->mmk_discount_price 
                        : $this->product->mmk_price }});
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(price || 0);
                }
            }" class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Image Gallery -->
                <div class="space-y-4">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg relative">
                        <!-- Discount Tag -->
                        <template x-if="selectedVariant?.discount > 0 || {{ $this->product->discount > 0 }}">
                            <div class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-md z-10">
                                <span x-text="selectedVariant?.discount ?? {{ $this->product->discount }}"></span>% OFF
                            </div>
                        </template>
                        <img :src="selectedVariant ? selectedVariant.image_url : currentImage" 
                             class="h-full w-full object-cover object-center">
                    </div>
                    @php
                        $images = collect();
                        if ($this->product->has_variant) {
                            foreach ($this->product->variants as $variant) {
                                $images = $images->merge($variant->getMedia('images'));
                            }
                        }
                        $images = $images->merge($this->product->getMedia('images'));
                    @endphp
                    @if($images->count() > 0)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($images as $media)
                            <img src="{{ $media->getUrl() }}"  @click="currentImage = '{{ $media->getUrl() }}'; if(!selectedVariant) { selectedVariant = null; }else { selectedVariant.image_url = '{{ $media->getUrl() }}';}"  class="h-20 w-20 rounded-md object-cover cursor-pointer hover:ring-2 hover:ring-slate-500">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $this->product->name }}</h2>

                    <!-- Category -->
                    <div class="text-sm text-gray-500">
                        {{ $this->product->category?->name }}
                    </div>

                    <!-- Variants Selection -->
                    @if($this->product->has_variant)
                        <div class="space-y-3">
                            <h3 class="text-sm font-medium text-gray-900">Available Variants</h3>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($this->product->variants as $variant)
                                    <button @click="
                                        selectedVariant = {
                                            id: {{ $variant->id }},
                                            name: '{{ $variant->name }}',
                                            price: {{ $variant->mmk_price }},
                                            discount_price: {{ $variant->mmk_discount_price }},
                                            discount: {{ $variant->discount }},
                                            image_url: '{{ $variant->getFirstMediaUrl('images') }}'
                                        };
                                        currentImage = selectedVariant.image_url;"
                                        :class="{
                                            'ring-2 ring-slate-500': selectedVariant?.id === {{ $variant->id }},
                                            'bg-slate-50': selectedVariant?.id === {{ $variant->id }}
                                        }"
                                        class="p-4 text-sm border rounded-md hover:border-slate-500 focus:outline-none">
                                        <div class="space-y-1">
                                            <div class="font-medium">{{ $variant->name }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                  

                        <!-- Variant Attributes -->
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
                        @endif
                    @else   
                        <!-- Product Attributes -->
                        @if($this->product->attributeValues)
                            <div class="space-y-3">
                                <h3 class="text-sm font-medium text-gray-900">Specifications</h3>
                                <dl class="space-y-2">
                                    @foreach($this->product->attributeValues as $attributeValue)
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

                    <!-- Pricing -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-gray-900">
                                <span x-text="formatPrice(getSelectedPrice())"></span> Ks
                            </p>
                            <template x-if="selectedVariant?.discount > 0 ">
                                <p class="text-sm text-gray-500 line-through" 
                                   x-text="formatPrice(selectedVariant?.price) + ' Ks'">
                                </p>
                            </template>
                            <template x-if="{{ $this->product->discount > 0 }}">
                                <p class="text-sm text-gray-500 line-through" 
                                   x-text="{{ $this->product->mmk_price }}) + ' Ks'">
                                </p>
                            </template>
                        </div>
                        <template x-if="selectedVariant?.discount > 0 ">
                            <p class="text-red-600 text-sm">
                                Save <span x-text="selectedVariant?.discount "></span>%
                            </p>
                        </template>
                        <template x-if=" {{ $this->product->discount > 0 }}">
                            <p class="text-red-600 text-sm">
                                Save <span x-text="{{ $this->product->discount }}"></span>%
                            </p>
                        </template>
                    </div>

                    <!-- MOQ -->
                    <div class="text-sm text-gray-500">
                        Minimum Order: {{ $this->product->MOQ }} pcs
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
                                   :min="{{ $this->product->MOQ }}">
                            <button @click="quantity++" 
                                    class="px-3 py-2 border-l hover:bg-gray-100">
                                +
                            </button>
                        </div>
                        <button type="button"
                                @click="$wire.addToCart(selectedVariant?.id ?? null, quantity)"
                                class="flex-1 bg-slate-600 text-white py-3 px-4 rounded-md hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                            Add to Cart
                        </button>
                    </div>

                 
                </div>
            </div>


            <div class="mt-10 font-thin font-mm " style="line-height: 2rem;"> {!! $this->product->description !!} </div>
        </div>
    </div>
</template>

<script>
    function number_format(number, decimals) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }
</script>