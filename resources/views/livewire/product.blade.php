<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="lg:text-5xl  font-bold mb-3 font-bebas ">Recommended Products</h1>
        
         <nav class="flex justify-between px-3.5 py-1 mb-4 ">
            <ol class="inline-flex items-center mb-3 space-x-1 text-xs text-neutral-500 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-medium sm:mb-0">
                <li class="flex items-center h-full"><a href="/" class="py-1 hover:text-neutral-900"><svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" ><path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" /></svg></a></li>  
                <svg class="w-5 h-5 text-gray-400/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="none" stroke="none"><path d="M10 8.013l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
                <li>
                    <a href="/" class="inline-flex items-center py-1 font-normal hover:text-neutral-900 focus:outline-none">Our Products</a>
                </li>
            </ol>
        </nav>
    
        <button @click="addToCart(1)">Add to Cart</button>
    
        <!-- Recommended Products Section -->
        <div wire:key="recommended-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 lg:gap-6 gap-4">
            @forelse ($this->recommendedProducts as $product)
                <article wire:key="recommended-{{ $product->id }}">
                    <div x-data="{ modalOpen: false }"
                    @keydown.escape.window="modalOpen = false" @click="modalOpen=true"
                    :class="{ 'z-1': modalOpen }" class="  w-auto h-auto p-2 overflow-hidden relative cursor-pointer ">
                        <!-- Discount Tag -->
                        @if(($product->has_variant && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
                            <div class="absolute text-sm  top-4 right-4 bg-slate-900 text-slate-100  px-2 py-1 rounded-md z-0">
                                {{ $product->has_variant ? $product->variants()->first()->discount : $product->discount }} % OFF
                            </div>
                        @endif
                        <img src="{{ $product->productImg }}"
                            class="rounded w-full object-cover transition duration-700 ease-out group-hover:scale-105"
                            alt="{{ $product->name }}" />
                        @livewire('product-detail', ['product' => $product])
                    </div>
                    <div class="flex flex-col flex-grow justify-between   lg:gap-3 gap-2  lg:p-6 p-3">
                        <span class="text-xs">{{ $product->category?->name }}</span>
    
                        @if($product->has_variant)
                            <h3 class="lg:text-sm  font-bold" aria-describedby="featureDescription">
                                {{ $product->variants()->first()->name }}
                            </h3>
                        @else
                            <h3 class="lg:text-smfont-bold" aria-describedby="featureDescription">
                                {{ $product->name }}
                            </h3>
                        @endif
    
                        <div class="flex flex-col lg:flex-row   lg:items-center gap-2">
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
    
                        <span class="text-xs"> MOQ : {{ $product->MOQ }} pcs</span>
    
                        <x-filament::button 
                            wire:key="add-to-cart-{{ $product->id }}"
                            color="gray"
                            wire:click.prevent="addToCart({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="addToCart({{ $product->id }})"
                        >
                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                Add to cart
                            </span>
                            <span wire:loading wire:target="addToCart({{ $product->id }})">
                                Adding...
                            </span>
                        </x-filament::button>
                    </div>
                </article>
            @empty
                <div wire:key="no-recommended">No Recommended Products Found</div>
            @endforelse
        </div>
    
       <div class="flex justify-between items-center">
            <h1 class="lg:text-5xl mt-10  font-bold mb-3 font-bebas ">Latest Products</h1>
            <nav class="flex justify-between px-3.5 py-1 mb-4 ">
               <ol class="inline-flex items-center mb-3 space-x-1 text-xs text-neutral-500 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-medium sm:mb-0">
                   <li class="flex items-center h-full"><a href="/" class="py-1 hover:text-neutral-900"><svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" ><path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" /></svg></a></li>  
                   <svg class="w-5 h-5 text-gray-400/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="none" stroke="none"><path d="M10 8.013l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
                   <li>
                       <a href="/" class="inline-flex items-center py-1 font-normal hover:text-neutral-900 focus:outline-none">Latest Products</a>
                   </li>
               </ol>
           </nav>
       </div>
    
        <!-- Latest Products Section -->
        <div wire:key="products-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 lg:gap-6 gap-4">
            @forelse ($this->products as $product)
                <article wire:key="product-latest-{{ $product->id }}">
                    <div x-data="{ modalOpen: false }"
                    @keydown.escape.window="modalOpen = false" @click="modalOpen=true"
                    :class="{ 'z-1': modalOpen }" class="  w-auto h-auto p-2 overflow-hidden relative cursor-pointer ">
                        <!-- Discount Tag -->
                        @if(($product->has_variant && $product->variants()->first()->discount > 0) || (!$product->has_variant && $product->discount > 0))
                            <div class="absolute text-sm  top-4 right-4 bg-slate-900 text-slate-100  px-2 py-1 rounded-md z-0">
                                {{ $product->has_variant ? $product->variants()->first()->discount : $product->discount }} % OFF
                            </div>
                        @endif
                        <img src="{{ $product->productImg }}"
                            class="rounded w-full object-cover transition duration-700 ease-out group-hover:scale-105"
                            alt="{{ $product->name }}" />
                        @livewire('product-detail', ['product' => $product])
                    </div>
                    <div class="flex flex-col flex-grow justify-between   lg:gap-3 gap-2  lg:p-6 p-3">
                        <span class="text-xs">{{ $product->category?->name }}</span>
    
                        @if($product->has_variant)
                            <h3 class="lg:text-sm  font-bold" aria-describedby="featureDescription">
                                {{ $product->variants()->first()->name }}
                            </h3>
                        @else
                            <h3 class="lg:text-smfont-bold" aria-describedby="featureDescription">
                                {{ $product->name }}
                            </h3>
                        @endif
    
                        <div class="flex flex-col lg:flex-row   lg:items-center gap-2">
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
    
                        <span class="text-xs"> MOQ : {{ $product->MOQ }} pcs</span>
    
                        <x-filament::button 
                            wire:key="add-to-cart-latest-{{ $product->id }}"
                            color="gray"
                            wire:click.prevent="addToCart({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="addToCart({{ $product->id }})"
                        >
                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                Add to cart
                            </span>
                            <span wire:loading wire:target="addToCart({{ $product->id }})">
                                Adding...
                            </span>
                        </x-filament::button>
                    </div>
                </article>
            @empty
                <div wire:key="no-products">No Products Found</div>
            @endforelse
        </div>
    
    </div>
</div>