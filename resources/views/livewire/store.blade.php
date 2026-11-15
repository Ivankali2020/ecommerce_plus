<div>
    @if (session()->has('error'))
        <x-shop.empty-state class="min-h-[70svh]" icon="heroicon-o-exclamation-triangle"
            :title="__('messages.error_title')" :description="__('messages.error_message')" />
    @elseif ($this->seller)
        @livewire('slider', ['seller' => $this->seller])
        @livewire('product', ['storeID' => $this->store_id])
    @else
        <x-shop.empty-state class="min-h-[70svh]" icon="heroicon-o-building-storefront" :title="__('messages.store_not_found')" />
    @endif
</div>
