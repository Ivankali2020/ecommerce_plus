<div class="space-y-4">
    <!-- COD Option (if available) -->
    @if($codAvailable)
    <div class="space-y-2">
        <h3 class="block text-sm font-medium text-gray-700 ">
            {{ __('messages.cart.payment_options') }}
        </h3>
        
        <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <input id="cod_option" wire:model.live="isCod" type="checkbox"
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-0 ring-0">
            <label for="cod_option" class="ml-3 block text-sm font-medium text-blue-700 ">
                {{ __('messages.cart.cash_on_delivery') }}
            </label>
        </div>
    </div>
    @endif

    <!-- Payment Methods (show only if not COD) -->
    @if(!$isCod)
    <div class="space-y-4">
        <div class="space-y-3">
            <h3 class="block text-sm font-medium text-gray-700 ">
                {{ __('messages.cart.payment_method') }}
            </h3>
            
            @if($this->payments->isEmpty())
                <div class="text-center py-4 text-gray-500">
                    <p class="text-sm ">
                        {{ __('messages.cart.no_payment_methods') }}
                    </p>
                </div>
            @else
                <!-- Payment Method Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-2">
                    @foreach($this->payments as $payment)
                    <label for="payment_{{ $payment->id }}" class="cursor-pointer">
                        <div class="relative border rounded-md p-2 transition-all hover:shadow-md
                            {{ $selectedPayment == $payment->id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            
                            <!-- Payment Logo -->
                            <div class="flex justify-center ">
                                @if($payment->getFirstMediaUrl('logos'))
                                    <img src="{{ $payment->getFirstMediaUrl('logos') }}" 
                                         alt="{{ $payment->name }}"
                                         class="w-12 h-12 object-contain rounded-lg ">
                                @else
                                    <!-- Default Payment Icon -->
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Radio Input (hidden) -->
                            <input type="radio" value="{{ $payment->id }}" wire:model.live="selectedPayment" 
                                id="payment_{{ $payment->id }}"
                                class="absolute top-2 right-2 h-3 w-3 rounded text-emerald-600 border-gray-300 focus:ring-0 focus:outline-none ">
                                
                            <!-- Loading Indicator for Selected Payment -->
                            @if($selectedPayment == $payment->id)
                                <div wire:loading wire:target="selectedPayment" class="absolute top-1 left-1">
                                    <x-filament::loading-indicator class="h-4 w-4" />
                                </div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            @endif
            
            @error('selectedPayment')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Payment Account Information -->
        @if($selectedPayment && $this->paymentAccounts->isNotEmpty())
        <div class="mt-6 space-y-3">
            <h4 class="text-sm font-medium text-gray-700 ">
                {{ __('messages.cart.choose_account') }}
            </h4>
            <div class="space-y-2">
                @foreach($this->paymentAccounts as $account)
                <label for="account-{{ $account->id }}" class="cursor-pointer">
                    <div class="relative flex items-center p-4 mb-2 border rounded-lg transition-all hover:shadow-md
                        {{ $selectedPaymentAccount == $account->id ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                        
                        <!-- Radio Input -->
                        <input id="account-{{ $account->id }}" type="radio" value="{{ $account->id }}" 
                            wire:model.live="selectedPaymentAccount"
                            class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-0 focus:outline-none rounded ">
                        
                        <!-- Account Details -->
                        <div class="ml-3 flex-grow">
                            <p class="text-sm font-medium text-gray-900 ">
                                {{ $account->name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-mono tracking-wider">{{ $account->number }}</span>
                            </p>

                            @if($account->payment->note)
                                <p class="text-xs font-mono text-red-500 mt-1">
                                    {{ $account->payment->note }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Loading Indicator for Selected Account -->
                        @if($selectedPaymentAccount == $account->id)
                            <div wire:loading wire:target="selectedPaymentAccount" class="absolute top-2 right-2">
                                <x-filament::loading-indicator class="h-4 w-4" />
                            </div>
                        @endif
                        
                        <!-- Copy Button -->
                        <button type="button"
                            x-data="{ copied: false }"
                            @click="
                                navigator.clipboard.writeText('{{ $account->number }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            class="ml-2 p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 transition-all">
                            <span x-show="!copied">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                            </span>
                            <span x-show="copied" x-cloak>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </label>
                @endforeach
            </div>
            
            @error('selectedPaymentAccount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <!-- Payment Slip Upload -->
        @if($selectedPaymentAccount)
        <div class="mt-6 space-y-3">
            <h4 class="text-sm font-medium text-gray-700 ">
                {{ __('messages.cart.payment_slip') }}
            </h4>
            
            <div x-data="{ fileName: '', isDragging: false }"
                class="relative border-2 border-dashed rounded-lg p-6 transition-all cursor-pointer
                @error('paymentSlip') border-red-300 bg-red-50 @else
                {{ $paymentSlip ? 'border-green-300 bg-green-50' : 'border-gray-300 hover:border-gray-400' }}
                @enderror"
                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="isDragging = false">
                
                <input type="file" wire:model="paymentSlip"
                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                    class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                    accept="image/*">
                
                <div class="text-center">
                    @if($paymentSlip)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-8 w-8 text-green-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-600 font-medium " x-text="fileName || 'File uploaded successfully'">
                            File uploaded successfully
                        </p>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-8 w-8 text-gray-400 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <p class="text-sm text-gray-600 ">
                            {{ __('messages.cart.upload_message') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 1MB</p>
                    @endif
                </div>
            </div>

            @if ($paymentSlip)
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <img src="{{ $paymentSlip->temporaryUrl() }}" 
                         alt="Payment Slip Preview"
                         class="w-full max-w-sm mx-auto rounded-lg shadow-sm">
                </div>
            @endif
            
            @error('paymentSlip')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @endif
    </div>
    @endif

    <!-- Show message if no address selected or delivery not available -->
    @if(!$codAvailable && !$this->payments->count())
    <div class="text-center py-6 text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400 mb-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
        </svg>
        <p class="text-sm ">
            {{ __('messages.cart.select_address_for_payment') }}
        </p>
    </div>
    @endif
</div>
