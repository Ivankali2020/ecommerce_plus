<div class="space-y-5">
    <div>
        <x-shop.label for="note">{{ __('messages.cart.note') }}</x-shop.label>
        <x-textarea id="note" wire:model="note" rows="3" :placeholder="__('messages.cart.note')" />
        <x-shop.field-error for="note" />
    </div>

    <x-shop.button size="lg" block wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout">
        <span wire:loading.remove wire:target="checkout" class="inline-flex items-center gap-2">
            <x-heroicon-o-lock-closed class="size-4" />
            {{ __('messages.cart.proceed_checkout') }}
        </span>
        <span wire:loading wire:target="checkout" class="inline-flex items-center gap-2">
            <x-filament::loading-indicator class="size-4" />
            {{ __('messages.cart.processing') }}…
        </span>
    </x-shop.button>

    @if ($errors->any())
        <div class="rounded-xl bg-sale/5 p-4 text-sm text-sale">
            <p class="flex items-center gap-2 font-medium">
                <x-heroicon-o-exclamation-circle class="size-5" />
                {{ __('messages.cart.validation_errors') }}
            </p>
            <ul class="mt-2 list-disc space-y-1 ps-9 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
