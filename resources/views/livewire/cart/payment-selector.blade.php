<div class="space-y-6">

    {{-- Cash on delivery --}}
    @if ($codAvailable)
        <div>
            <x-shop.label>{{ __('messages.cart.payment_options') }}</x-shop.label>
            <x-shop.option-card :selected="$isCod">
                <span class="flex items-center gap-3 text-sm text-ink">
                    <input type="checkbox" wire:model.live="isCod" class="size-4 accent-ink">
                    <x-heroicon-o-banknotes class="size-5 text-accent" />
                    {{ __('messages.cart.cash_on_delivery') }}
                </span>
            </x-shop.option-card>
        </div>
    @endif

    @unless ($isCod)
        {{-- Payment method --}}
        <div>
            <x-shop.label>{{ __('messages.cart.payment_method') }}</x-shop.label>

            @if ($this->payments->isEmpty())
                <p class="text-sm text-ink-soft">{{ __('messages.cart.no_payment_methods') }}</p>
            @else
                <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-3">
                    @foreach ($this->payments as $payment)
                        <x-shop.option-card wire:key="payment-{{ $payment->id }}" :selected="$selectedPayment == $payment->id"
                            class="flex aspect-square items-center justify-center p-3!" title="{{ $payment->name }}">
                            <input type="radio" value="{{ $payment->id }}" wire:model.live="selectedPayment" class="sr-only">
                            @if ($logo = $payment->getFirstMediaUrl('logos'))
                                <img src="{{ $logo }}" alt="{{ $payment->name }}" class="size-full rounded-lg object-contain">
                            @else
                                <span class="flex flex-col items-center gap-1 text-center text-[11px] text-ink-soft">
                                    <x-heroicon-o-credit-card class="size-6 text-muted" />
                                    {{ $payment->name }}
                                </span>
                            @endif
                            @if ($selectedPayment == $payment->id)
                                <x-heroicon-s-check-circle class="absolute -right-1.5 -top-1.5 size-5 rounded-full bg-surface text-ink" />
                            @endif
                        </x-shop.option-card>
                    @endforeach
                </div>
            @endif

            <x-shop.field-error for="selectedPayment" />
        </div>

        {{-- Account --}}
        @if ($selectedPayment && $this->paymentAccounts->isNotEmpty())
            <div>
                <x-shop.label>{{ __('messages.cart.choose_account') }}</x-shop.label>
                <div class="space-y-2">
                    @foreach ($this->paymentAccounts as $account)
                        <x-shop.option-card wire:key="account-{{ $account->id }}" :selected="$selectedPaymentAccount == $account->id">
                            <div class="flex items-center gap-3">
                                <input type="radio" value="{{ $account->id }}" wire:model.live="selectedPaymentAccount" class="size-4 shrink-0 accent-ink">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-ink">{{ $account->name }}</p>
                                    <p class="font-mono text-sm tracking-wider text-ink-soft">{{ $account->number }}</p>
                                    @if ($account->payment->note)
                                        <p class="mt-1 text-xs text-sale">{{ $account->payment->note }}</p>
                                    @endif
                                </div>
                                <button type="button"
                                    x-data="{ copied: false }"
                                    @click.prevent="navigator.clipboard.writeText(@js($account->number)); copied = true; setTimeout(() => copied = false, 2000)"
                                    aria-label="{{ __('messages.cart.copy_account_number') }}"
                                    class="flex size-9 shrink-0 items-center justify-center rounded-full text-muted transition-colors hover:bg-ink/5 hover:text-ink">
                                    <x-heroicon-o-clipboard-document x-show="! copied" class="size-4" />
                                    <x-heroicon-o-check x-show="copied" x-cloak class="size-4 text-accent" />
                                </button>
                            </div>
                        </x-shop.option-card>
                    @endforeach
                </div>

                <x-shop.field-error for="selectedPaymentAccount" />
            </div>
        @endif

        {{-- Payment slip --}}
        @if ($selectedPaymentAccount)
            <div>
                <x-shop.label>{{ __('messages.cart.payment_slip') }}</x-shop.label>

                <div x-data="{ dragging: false }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop="dragging = false"
                    :class="dragging && 'border-ink bg-canvas'"
                    @class([
                        'relative rounded-xl border border-dashed px-4 py-8 text-center transition-colors',
                        'border-sale bg-sale/5' => $errors->has('paymentSlip'),
                        'border-accent bg-accent-soft/50' => ! $errors->has('paymentSlip') && $paymentSlip,
                        'border-line hover:border-ink/40' => ! $errors->has('paymentSlip') && ! $paymentSlip,
                    ])>
                    <input type="file" wire:model="paymentSlip" accept="image/*" class="absolute inset-0 size-full cursor-pointer opacity-0">

                    <div wire:loading.remove wire:target="paymentSlip">
                        @if ($paymentSlip)
                            <x-heroicon-o-check-circle class="mx-auto mb-2 size-7 text-accent" />
                            <p class="text-sm text-ink">{{ __('messages.cart.slip_uploaded') }}</p>
                        @else
                            <x-heroicon-o-arrow-up-tray class="mx-auto mb-2 size-7 text-muted" />
                            <p class="text-sm text-ink-soft">{{ __('messages.cart.upload_message') }}</p>
                            <p class="mt-1 text-xs text-muted">{{ __('messages.cart.slip_hint') }}</p>
                        @endif
                    </div>
                    <x-filament::loading-indicator wire:loading wire:target="paymentSlip" class="mx-auto size-6 text-accent" />
                </div>

                @if ($paymentSlip && ! $errors->has('paymentSlip'))
                    <img src="{{ $paymentSlip->temporaryUrl() }}" alt="{{ __('messages.cart.payment_slip') }}"
                        class="mx-auto mt-4 max-h-80 rounded-xl border border-line object-contain">
                @endif

                <x-shop.field-error for="paymentSlip" />
            </div>
        @endif
    @endunless

    @if (! $codAvailable && $this->payments->isEmpty())
        <div class="flex flex-col items-center gap-2 py-4 text-center text-sm text-ink-soft">
            <x-heroicon-o-banknotes class="size-7 text-muted" />
            {{ __('messages.cart.select_address_for_payment') }}
        </div>
    @endif
</div>
