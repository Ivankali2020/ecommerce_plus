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
                <div class="absolute z-10  inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="bg-red-500 text-white px-3 py-1 rounded-md font-medium transform -rotate-12 text-[10px] md:text-xs">
                        {{ __('messages.out_of_stock') }}
                    </div>
                </div>
            @endif

            <!-- Available Stock Indicator -->
            @if($product->quantity > 0 && !$product->has_variant)
                <div class="absolute z-10 bottom-2 left-2 flex items-center justify-center">
                    <div class="bg-gray-500 text-white px-3 py-1 rounded-md font-medium transform text-[12px] md:text-sm">
                       {{  $product->quantity }} <span class="text-[10px] md:text-xs">{{ __('messages.in_stock') }}</span>
                    </div>
                </div>
            @endif
                
            <img src="{{ $product->productImg }}"
                 class="rounded w-full h-32 md:h-48 lg:h-60 object-cover transition duration-700 ease-out group-hover:scale-105"
                 alt="{{ $product->name }}" />
        </a>

        <div class="flex flex-col flex-grow justify-between lg:gap-3 gap-2 lg:p-6 p-3 relative">

            {{-- Wholesale Badge --}}
            @if($type === 'wholesale')
                <div class="absolute top-2 right-2 z-10">
                   {{-- wholesale icon svg --}}
                    <div class=" px-2 py-1 text-[10px] md:text-xs  flex items-center gap-1">
                       <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" width="20px" height="20px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <polygon style="fill:#6F6F6F;" points="512,151.978 256,1.618 235.101,279.411 256,489.483 441.928,510.382 512,510.382 "></polygon> <g> <polygon style="fill:#9D9D9D;" points="0,151.978 0,510.382 70.072,510.382 256,489.483 256,1.618 "></polygon> <polygon style="fill:#9D9D9D;" points="441.928,187.036 256,187.036 235.101,350.907 256,510.382 441.928,510.382 "></polygon> </g> <rect x="70.071" y="187.037" style="fill:#D4D4D4;" width="185.929" height="323.344"></rect> <polygon style="fill:#FFC222;" points="347.613,510.382 393.42,510.382 393.42,418.77 326.715,397.87 "></polygon> <polygon style="fill:#FEDF77;" points="280.907,397.87 301.806,510.382 347.613,510.382 347.613,418.77 "></polygon> <polygon style="fill:#FFC222;" points="235.101,397.87 256,510.382 301.806,510.382 301.806,418.77 "></polygon> <polygon style="fill:#FEDF77;" points="189.294,397.87 210.194,510.382 256,510.382 256,418.77 "></polygon> <polygon style="fill:#FFC222;" points="143.487,397.87 164.386,510.382 210.194,510.382 210.194,418.77 "></polygon> <polygon style="fill:#FEDF77;" points="118.58,418.77 118.58,510.382 164.386,510.382 164.386,397.87 "></polygon> <polygon style="fill:#FF9500;" points="347.613,418.77 393.42,418.77 393.42,327.156 326.715,306.257 "></polygon> <polygon style="fill:#FFC222;" points="301.806,418.77 347.613,418.77 347.613,327.156 280.907,306.257 "></polygon> <polygon style="fill:#FF9500;" points="235.101,306.257 256,418.77 301.806,418.77 301.806,327.156 "></polygon> <polygon style="fill:#FFC222;" points="189.294,306.257 210.194,418.77 256,418.77 256,327.156 "></polygon> <polygon style="fill:#FF9500;" points="143.487,306.257 164.386,418.77 210.194,418.77 210.194,327.156 "></polygon> <g> <polygon style="fill:#FFC222;" points="164.386,306.257 118.58,327.156 118.58,418.77 164.386,418.77 "></polygon> <polygon style="fill:#FFC222;" points="347.613,235.543 326.715,281.349 347.613,327.156 393.42,327.156 393.42,235.543 "></polygon> </g> <polygon style="fill:#FEDF77;" points="347.613,327.156 347.613,235.543 301.806,235.543 280.907,281.349 301.806,327.156 "></polygon> <polygon style="fill:#FFC222;" points="301.806,327.156 301.806,235.543 256,235.543 235.101,279.513 256,327.156 "></polygon> <polygon style="fill:#FEDF77;" points="256,327.156 256,235.543 210.194,235.543 189.294,281.349 210.194,327.156 "></polygon> <polygon style="fill:#FFC222;" points="210.194,327.156 210.194,235.543 164.386,235.543 143.487,281.349 164.386,327.156 "></polygon> <rect x="118.575" y="235.541" style="fill:#FEDF77;" width="45.806" height="91.617"></rect> </g></svg>
                    </div>
                </div>
            @endif

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
