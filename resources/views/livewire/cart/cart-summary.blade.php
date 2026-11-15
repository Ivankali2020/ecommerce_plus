<div>
    <x-shop.panel :title="__('messages.cart.order_summary')" class="animate-fade-in">
        <div class="space-y-3">
            <x-shop.summary-row :label="__('messages.cart.subtotal')">
                {{ number_format($this->subtotal) }} {{ __('messages.mmk') }}
            </x-shop.summary-row>

            @if ($deliveryFee > 0)
                <x-shop.summary-row :label="__('messages.cart.delivery_fee')">
                    {{ number_format($deliveryFee) }} {{ __('messages.mmk') }}
                </x-shop.summary-row>
            @endif

            <x-shop.summary-row :label="__('messages.cart.total')" emphasis class="border-t border-line pt-4">
                {{ number_format($this->total) }} <span class="font-sans text-xs text-ink-soft">{{ __('messages.mmk') }}</span>
            </x-shop.summary-row>
        </div>

        @if ($this->pointsToEarn > 0)
            <div class="mt-6 rounded-xl bg-accent-soft p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-ink">{{ __('messages.cart.earn_points_title') }}</p>
                        <p class="mt-1 text-xs text-ink-soft">{{ __('messages.cart.earn_points_subtitle') }}</p>
                    </div>
                    <p class="shrink-0 font-display text-2xl text-accent-dark">
                        +{{ number_format($this->pointsToEarn) }}
                        <span class="font-sans text-[10px] uppercase tracking-[0.15em]">{{ __('messages.cart.points') }}</span>
                    </p>
                </div>

                @auth
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 border-t border-accent/20 pt-3 text-xs text-ink-soft">
                        <span>{{ __('messages.cart.current_balance') }}: <span class="tabular-nums">{{ number_format(auth()->user()->points ?? 0) }}</span></span>
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-shield-check class="size-4 text-accent" />
                            {{ __('messages.cart.after_order') }}: <span class="tabular-nums">{{ number_format((auth()->user()->points ?? 0) + $this->pointsToEarn) }}</span>
                        </span>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="mt-3 inline-block text-xs text-accent-dark underline underline-offset-4">
                        {{ __('messages.cart.login_to_earn_points') }}
                    </a>
                @endauth
            </div>
        @endif
    </x-shop.panel>
</div>
