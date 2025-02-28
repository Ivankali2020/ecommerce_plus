<div wire:key="product-item-{{ $type }}-{{ $product->id }}" class="animate-fade-in-up">

    <article  x-data="{ modalOpen: false }" class="group rounded-md sm:justify-between font-jost flex rounded-radius max-w-sm flex-col overflow-hidden shadow-sm relative">
    
    <div @keydown.escape.window="modalOpen = false" 
         @click="modalOpen=true"
         :class="{ 'z-1': modalOpen }" 
         class="w-auto h-auto p-2 overflow-hidden relative cursor-pointer">
        
        {{-- Discount Tag --}}
        @if(($product->has_variant && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
            <div class="absolute text-sm top-4 right-4 bg-slate-900 text-slate-100 px-2 py-1 rounded-md z-10">
                {{ $product->has_variant ? $product->variants()->first()->discount : $product->discount }} % OFF
            </div>
        @endif

        <img src="{{ $product->productImg }}"
             class="rounded w-full object-cover transition duration-700 ease-out group-hover:scale-105"
             alt="{{ $product->name }}" />
             
        {{-- Use wire:key for the product detail component --}}
        @livewire('product-detail', ['product' => $product, 'type' => $type], key('product-detail-{{ $type }}'.$product->id))
    </div>
    

    <div class="flex flex-col flex-grow justify-between lg:gap-3 gap-2 lg:p-6 p-3">
        <span class="text-xs">{{ $product->category?->name }}</span>

        <h3 class="lg:text-sm font-bold" aria-describedby="featureDescription">
            {{ $product->has_variant ? $product->variants()->first()->name : $product->name }}
        </h3>

        <div class="flex flex-col lg:flex-row lg:items-center gap-2">
            @if($product->has_variant)
                @if($product->variants()->first()->discount > 0)
                    <p class="text-slate-900 font-bold text-lg">
                        {{ number_format($product->variants()->first()->mmk_discount_price, 0) }} Ks
                    </p>
                    <p class="text-gray-400 line-through text-sm">
                        {{ number_format($product->variants()->first()->mmk_price, 0) }} Ks
                    </p>
                @else
                    <p class="text-gray-600">
                        {{ number_format($product->variants()->first()->mmk_price, 0) }} Ks
                    </p>
                @endif
            @else
                @if($product->discount > 0)
                    <p class="text-slate-900 font-bold text-lg">
                        {{ number_format($product->mmk_discount_price, 0) }} Ks
                    </p>
                    <p class="text-gray-400 line-through text-sm">
                        {{ number_format($product->mmk_price, 0) }} Ks
                    </p>
                @else
                    <p class="text-gray-600">
                        {{ number_format($product->mmk_price, 0) }} Ks
                    </p>
                @endif
            @endif
        </div>

        <span class="text-xs">MOQ : {{ $product->MOQ }} pcs</span>

        {{-- Update the add to cart button to emit to parent --}}
        <x-filament::button 
            x-data
            color="gray"
            {{-- @click="$dispatch('add-to-cart', { productId: {{ $product->id }} })" --}}
            wire:loading.attr="disabled"
            wire:click="addToCart({{ $product->id }})"
            wire:target="addToCart({{ $product->id }})">
            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                Add to cart
            </span>
            <span wire:loading wire:target="addToCart({{ $product->id }})">
                Adding...
            </span>
        </x-filament::button>
    </div>
</article>

</div>