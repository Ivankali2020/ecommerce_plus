<div>
    @if (session()->has('error'))
        <div class="flex justify-center items-center min-h-svh flex-col gap-2 animate-fade-in-up">
            <p class="font-bold font-bebas text-4xl text-red-600">
                {{ __('messages.error_title') }}
            </p>
            <p class="text-sm font-jost text-slate-600">
                {{  __('messages.error_message') }}
            </p>
        </div>
    @else
        @if($this->seller)
            @livewire('slider', ['seller' => $this->seller])
            @livewire('product', ['storeID' => $this->store_id])
        @else
            <div class="flex justify-center items-center min-h-svh flex-col gap-2">
                <p class="font-bold font-bebas text-4xl text-red-600">
                    {{ __('messages.store_not_found') }}
                </p>
            </div>
        @endif
    @endif
</div>
