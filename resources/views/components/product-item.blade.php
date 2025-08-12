<div wire:key="product-item-{{ $type }}-{{ $product->id }}" class="animate-fade-in-up">
    <article  class="group rounded-md sm:justify-between font-jost flex rounded-radius max-w-sm flex-col overflow-hidden shadow-sm relative">
        <a href="{{ route('product', $product->id ) }}" wire:navigate class="w-auto h-auto p-2 overflow-hidden relative cursor-pointer">
            {{-- Improved Discount Tag --}}
            @if(($product->has_variant && $product->variants()->first() && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
                <div class="absolute top-0 right-0 z-10">
                    <div class="bg-red-500 text-white px-3 py-1 text-[10px] md:text-xs rounded-md shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                        <span class="font-bold  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->discount ?? 0 : $product->discount ?? '0' }}%
                        </span>
                        <span class="selection:{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.off') }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Out of Stock Indicator -->
            @if($product->quantity <= 0 && !$product->has_variant)
                <div class="absolute z-20  inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="bg-red-500 text-white px-3 py-1 rounded-md font-medium transform -rotate-12 text-[10px] md:text-xs">
                        {{ __('messages.out_of_stock') }}
                    </div>
                </div>
            @endif

            <!-- Available Stock Indicator -->
            @if($product->quantity > 0 && !$product->has_variant)
                <div class="absolute z-20 bottom-2 left-2 flex items-center justify-center">
                    <div class="bg-gray-500 text-white px-3 py-1 rounded-md font-medium transform text-[12px] md:text-sm">
                       {{  $product->quantity }} <span class="text-[10px] md:text-xs">{{ __('messages.in_stock') }}</span>
                    </div>
                </div>
            @endif
                
            <img src="{{ $product->productImg }}"
                 class="rounded w-full object-cover transition duration-700 ease-out group-hover:scale-105"
                 alt="{{ $product->name }}" />
        </a>

        <div class="flex flex-col flex-grow justify-between lg:gap-3 gap-2 lg:p-6 p-3">
            <span class="hidden md:block text-xs "">{{ $product->category?->name }}</span>    

            <h3 class="text-sm lg:text-lg font-bold" aria-describedby="featureDescription">
                {{ $product->has_variant && $product->variants()->first() ? $product->variants()->first()->name : $product->name }}
            </h3>

            <div class="flex flex-col lg:flex-row lg:items-center gap-2">
                @if($product->has_variant && $product->variants()->first())
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

            <x-filament::button
                x-data
                color="gray"
                class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                wire:loading.attr="disabled"
                wire:click="addToCart({{ $product->id }})"
                wire:target="addToCart({{ $product->id }})">
                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                    {{ __('messages.add_to_cart') }}
                </span>
                <span wire:loading wire:target="addToCart({{ $product->id }})">
                    {{ __('messages.adding_to_cart') }}
                </span>
            </x-filament::button>
        </div>
    </article>
</div>
