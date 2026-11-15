<div class="shop-container py-10 md:py-16">
    @if ($cartItems->isEmpty())
        <x-shop.empty-state icon="heroicon-o-shopping-bag"
            :title="__('messages.cart.empty_cart')"
            :description="__('messages.cart.start_adding')">
            <x-shop.button :href="route('welcome')" wire:navigate>
                {{ __('messages.cart.continue_shopping') }}
                <x-heroicon-m-arrow-long-right class="size-4" />
            </x-shop.button>
        </x-shop.empty-state>
    @else
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7 xl:col-span-8">
                <livewire:cart.cart-items />
            </div>

            <div class="space-y-6 lg:col-span-5 xl:col-span-4">
                <livewire:cart.cart-summary />

                <x-shop.panel :title="__('messages.cart.checkout_process')">
                    <div class="space-y-8">
                        <livewire:cart.address-selector />
                        <livewire:cart.payment-selector />
                        <livewire:cart.checkout-form />
                    </div>
                </x-shop.panel>
            </div>
        </div>
    @endif
</div>
