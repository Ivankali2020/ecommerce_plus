<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="lg:w-2/3">
            <h2 class="text-3xl font-bebas mb-6">Shopping Cart</h2>

            @forelse($cartItems as $item)
                <div wire:key="cart-item-{{ $item->id }}"
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 mb-4 bg-white rounded-lg shadow-sm animate-fade-in-up">
                    <!-- Product Image -->
                    <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                        class="w-24 h-24 object-cover rounded-lg">

                    <!-- Product Details -->
                    <div class="flex-grow">
                        <h3 class="font-bold text-lg">{{ $item->product->name }}</h3>
                        @if($item->variant)
                            <p class="text-sm text-gray-600">Variant: {{ $item->variant->name }}</p>
                        @endif
                        <p class="text-sm text-gray-500">{{ $item->product->category?->name }}</p>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
                            -
                        </button>
                        <span class="w-12 text-center">{{ $item->quantity }}</span>
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
                            +
                        </button>
                    </div>

                    <!-- Price -->
                    <div class="text-right">
                        <p class="font-bold">
                            {{ number_format($item->price * $item->quantity) }} Ks
                        </p>
                        @if($item->variant && $item->variant->discount > 0)
                            <p class="text-sm text-gray-400 line-through">
                                {{ number_format($item->variant->price * $item->quantity) }} Ks
                            </p>
                        @endif
                    </div>

                    <!-- Remove Button -->
                    <button wire:click="removeItem({{ $item->id }})"
                        class="text-red-500 hover:text-red-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            @empty
                <div class="text-center py-12 animate-fade-in">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Start adding some items to your cart.</p>
                    <div class="mt-6">
                        <a href="/"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Order Summary -->
        @if($cartItems->isNotEmpty())
                <div class="lg:w-1/3">
                    <div class="bg-white p-6 rounded-lg shadow-sm animate-fade-in">
                        <h2 class="text-2xl font-bebas mb-4">Order Summary</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-bold">
                                    {{ number_format($cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            })) }} Ks
                                </span>
                            </div>
                            <hr>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>
                                    {{ number_format($cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            })) }} Ks
                                </span>
                            </div>
                            <button class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors">
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
        @endif
    </div>
</div>