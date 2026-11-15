<div>
    <x-shop.label>{{ __('messages.cart.select_address') }}</x-shop.label>

    <div class="space-y-3">
        @forelse ($this->addresses as $address)
            <x-shop.option-card wire:key="address-{{ $address->id }}" :selected="$selectedAddress == $address->id">
                <div class="flex items-start gap-3">
                    <input type="radio" name="address" value="{{ $address->id }}" wire:model.live="selectedAddress"
                        class="mt-1 size-4 shrink-0 accent-ink">
                    <div class="min-w-0 flex-1 text-sm">
                        <p class="text-ink">{{ $address->address }}</p>
                        <p class="mt-1 text-ink-soft">{{ $address->phone }}</p>
                        <p class="mt-1 text-xs text-muted">{{ $address->township->mm_name }}, {{ $address->region->mm_name }}</p>
                    </div>
                    <x-filament::loading-indicator wire:loading wire:target="selectedAddress" class="size-4 shrink-0 text-accent" />
                </div>
            </x-shop.option-card>
        @empty
            <a href="{{ route('addresses') }}"
                class="flex flex-col items-center gap-2 rounded-xl border border-dashed border-line px-4 py-6 text-center transition-colors hover:border-ink/40">
                <x-heroicon-o-map-pin class="size-6 text-accent" />
                <span class="text-sm text-ink">{{ __('messages.cart.no_addresses') }}</span>
                <span class="text-xs text-muted">{{ __('messages.cart.create_new_address') }}</span>
            </a>
        @endforelse
    </div>

    {{-- Delivery availability for the chosen address --}}
    @if ($selectedAddress && $deliveryDetails)
        @php
            $isUnavailable = isset($deliveryDetails['unavailable']);
        @endphp

        <div @class([
            'mt-4 space-y-3 rounded-xl p-4 text-sm',
            'bg-sale/5 text-sale' => $isUnavailable,
            'bg-canvas text-ink-soft' => ! $isUnavailable,
        ])>
            <p class="flex items-center gap-2 font-medium">
                @if ($isUnavailable)
                    <x-heroicon-o-exclamation-circle class="size-5" />
                    {{ __('messages.cart.delivery_unavailable') }}
                @else
                    <x-heroicon-o-check-circle class="size-5 text-accent" />
                    <span class="text-ink">{{ __('messages.cart.delivery_available') }}</span>
                @endif
            </p>

            @unless ($isUnavailable)
                <x-shop.summary-row :label="__('messages.cart.delivery_fee')">
                    {{ number_format($deliveryDetails['fees']) }} {{ __('messages.mmk') }}
                </x-shop.summary-row>

                <p class="flex items-center gap-2 text-xs">
                    @if ($deliveryDetails['is_cod'])
                        <x-heroicon-o-banknotes class="size-4 text-accent" />
                        {{ __('messages.cart.cod_available') }}
                    @else
                        <x-heroicon-o-no-symbol class="size-4 text-muted" />
                        {{ __('messages.cart.cod_not_available') }}
                    @endif
                </p>

                @if ($deliveryDetails['remark'])
                    <p class="text-xs italic text-muted">{{ $deliveryDetails['remark'] }}</p>
                @endif
            @endunless
        </div>
    @endif

    <x-shop.field-error for="selectedAddress" />
</div>
