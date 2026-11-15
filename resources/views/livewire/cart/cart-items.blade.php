@php
    $subtotal = $cartItems->sum(fn ($item) => $item->price * $item->quantity);
    $deliveryFees = (float) ($delivery['fees'] ?? 0);
@endphp

<div x-data="{ view: 'items' }">
    <div class="mb-2 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
        <h1 class="font-display text-4xl font-medium text-ink">
            {{ __('messages.cart.shopping_cart') }}
            <span class="ms-1 align-middle font-sans text-sm text-muted tabular-nums">({{ $cartItems->sum('quantity') }})</span>
        </h1>

        <div class="flex rounded-full border border-line bg-surface p-1 text-xs">
            @foreach (['items' => __('messages.cart.view_items'), 'receipt' => __('messages.cart.view_receipt')] as $value => $label)
                <button type="button" @click="view = '{{ $value }}'"
                    class="rounded-full px-4 py-1.5 transition-colors"
                    :class="view === '{{ $value }}' ? 'bg-ink text-canvas' : 'text-ink-soft hover:text-ink'">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Items --}}
    <div x-show="view === 'items'" class="divide-y divide-line">
        @forelse ($cartItems as $item)
            <x-shop.cart-line :item="$item" />
        @empty
            <x-shop.empty-state icon="heroicon-o-shopping-bag"
                :title="__('messages.cart.empty_cart')"
                :description="__('messages.cart.start_adding')">
                <x-shop.button :href="route('welcome')" wire:navigate>{{ __('messages.cart.continue_shopping') }}</x-shop.button>
            </x-shop.empty-state>
        @endforelse
    </div>

    {{-- Receipt --}}
    @if ($cartItems->isNotEmpty())
        <div x-show="view === 'receipt'" x-cloak class="mt-6">
            <div class="mx-auto max-w-lg rounded-2xl border border-line bg-surface p-6 font-mono text-sm text-ink md:p-8">
                <div class="mb-6 border-b border-dashed border-line pb-6 text-center">
                    <p class="font-display text-2xl not-italic">{{ $tenantSellerName ?? config('app.name') }}</p>
                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-muted">{{ __('messages.cart.receipt_title') }}</p>
                </div>

                <dl class="mb-6 grid grid-cols-2 gap-y-1 text-xs text-ink-soft">
                    <dt>{{ __('messages.cart.receipt_date') }}</dt>
                    <dd class="text-end">{{ now()->format('d M Y, h:i A') }}</dd>
                    <dt>{{ __('messages.cart.item_count') }}</dt>
                    <dd class="text-end tabular-nums">{{ $cartItems->count() }}</dd>
                    <dt>{{ __('messages.cart.total_quantity') }}</dt>
                    <dd class="text-end tabular-nums">{{ $cartItems->sum('quantity') }}</dd>
                    @if (! empty($delivery['type']))
                        <dt>{{ __('messages.navigation.delivery') }}</dt>
                        <dd class="text-end">{{ ucfirst($delivery['type']) }}</dd>
                    @endif
                </dl>

                <ul class="space-y-3 border-y border-dashed border-line py-5">
                    @foreach ($cartItems as $item)
                        <li wire:key="receipt-{{ $item->id }}" class="flex justify-between gap-4">
                            <div class="min-w-0">
                                <p class="truncate">{{ $item->product->name }}</p>
                                <p class="text-xs text-muted">
                                    @if ($item->variant){{ $item->variant->name }} · @endif{{ $item->quantity }} × {{ number_format($item->price) }}
                                </p>
                            </div>
                            <span class="shrink-0 tabular-nums">{{ number_format($item->price * $item->quantity) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="space-y-2 pt-5">
                    <div class="flex justify-between">
                        <span>{{ __('messages.cart.subtotal') }}</span>
                        <span class="tabular-nums">{{ number_format($subtotal) }} {{ __('messages.mmk') }}</span>
                    </div>

                    @if ($deliveryFees > 0)
                        <div class="flex justify-between text-ink-soft">
                            <span>{{ __('messages.cart.delivery_fee') }}</span>
                            <span class="tabular-nums">{{ number_format($deliveryFees) }} {{ __('messages.mmk') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-dashed border-line pt-3 text-base font-semibold">
                            <span>{{ __('messages.cart.grand_total') }}</span>
                            <span class="tabular-nums">{{ number_format($subtotal + $deliveryFees) }} {{ __('messages.mmk') }}</span>
                        </div>
                    @else
                        <p class="pt-2 text-xs text-muted">{{ __('messages.cart.fees_calculated_at_checkout') }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
