<div class="container mb-20 lg:mb-0 mx-auto px-0 sm:px-4 py-4 sm:py-8">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
        
        <!-- Cart Items Section -->
        @if ($cartItems->isNotEmpty())
            <livewire:cart.cart-items />
        @else
            <div class="w-full p-4 lg:p-0 lg:w-3/5 2xl:w-2/3">
                <div class="text-center py-12 animate-fade-in">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 ">
                        {{ __('messages.cart.empty_cart') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 ">
                        {{ __('messages.cart.start_adding') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('welcome') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-0 focus:ring-offset-0 ">
                            {{ __('messages.cart.continue_shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Checkout Section -->
        @if ($cartItems->isNotEmpty())
            <div class="w-full lg:w-2/5 2xl:w-1/3 font-jost">
                <div class="">
                    
                    <!-- Cart Summary -->
                    <livewire:cart.cart-summary />

                    <!-- Checkout Process -->
                    <div class="bg-white p-4 lg:p-6  rounded-lg shadow-sm animate-fade-in ">
                        <h2 class="text-2xl font-bebas mb-4 ">
                            {{ __('messages.cart.checkout_process') }}
                        </h2>

                        <div class="space-y-6">
                            <!-- Address Selection (includes delivery lookup) -->
                            <livewire:cart.address-selector />

                            <!-- Payment Selection -->
                            <livewire:cart.payment-selector />

                            <!-- Checkout Form -->
                            <livewire:cart.checkout-form />
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
