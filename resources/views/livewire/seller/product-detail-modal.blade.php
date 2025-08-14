<div>
    <x-slot name="heading">
        {{ $selectedProduct->name ?? 'Product Details' }}
    </x-slot>
    
    <div class="space-y-4">
        @if($selectedProduct)
            
            @if($selectedProduct->variants->count() > 0)
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($selectedProduct->variants as $variant)
            <div wire:key="variant_{{ $variant->sku}}" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class=" bg-gray-200 dark:bg-gray-700 relative">
                    @if($variant->discount > 0)
                    <div class="absolute top-0 right-0 z-10">
                        <div class="bg-red-500 text-white px-3 py-1 rounded-full shadow-md transform transition-transform duration-300 hover:scale-105 flex items-center gap-1">
                            <span class="font-bold text-xs  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ $variant->discount ?? '0'  }}%
                            </span>
                            <span class="text-xs {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.off') }}
                            </span>
                        </div>
                    </div>
                @endif
                    @if($variant->getFirstMediaUrl('images'))
                        <img src="{{ $variant->getFirstMediaUrl('images') }}" alt="{{ $variant->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h6 class="text-xs">MOQ : {{ $variant->MOQ }} pcs</span>

                    <h3 class="text-xs my-1 font-bold" aria-describedby="featureDescription">
                        {{ $variant->name  }}
                    </h3>
        
                    <div class="flex flex-col lg:flex-row lg:items-center gap-2">
                        @if($variant->discount > 0)
                        <p class="text-slate-900 ">
                            {{ number_format($variant->mmk_discount_price, 0) }} Ks
                        </p>
                        <p class="text-gray-400 line-through text-xs">
                            {{ number_format($variant->mmk_price, 0) }} Ks
                        </p>
                    @else
                        <p class="text-gray-600">
                            {{ number_format($variant->mmk_price, 0) }} Ks
                        </p>
                    @endif
                    </div>
                    <div class=" mt-2">
                        <x-filament::button
                                x-data size="sm"
                                color="gray"
                                class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                                wire:loading.attr="disabled"
                                wire:click="addToCart({{ $selectedProduct->id }}, {{ $variant->id }}, {{ $variant->MOQ}})"
                                wire:target="addToCart({{ $selectedProduct->id }}, {{ $variant->id }}, {{ $variant->MOQ}})">
                                <span wire:loading.remove wire:target="addToCart({{ $selectedProduct->id }}, {{ $variant->id }}, {{ $variant->MOQ}})">
                                    {{ __('messages.add_to_cart') }}
                                </span>
                                <span wire:loading wire:target="addToCart({{ $selectedProduct->id }}, {{ $variant->id }}, {{ $variant->MOQ}})">
                                    {{ __('messages.adding_to_cart') }}
                                </span>
                            </x-filament::button>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
            @endif
        @endif
    </div>
    
    
</div>
