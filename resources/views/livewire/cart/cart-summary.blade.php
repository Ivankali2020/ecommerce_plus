<div class="bg-white p-4 lg:p-6 rounded-lg shadow-sm animate-fade-in">
    <h2 class="text-2xl font-bebas mb-4 ">
        {{ __('messages.cart.order_summary') }}
    </h2>
    
    <div class="space-y-4">
        <div class="flex justify-between">
            <span class="text-gray-600 ">
                {{ __('messages.cart.subtotal') }}
            </span>
            <span class="font-bold">{{ number_format($this->subtotal) }} Ks</span>
        </div>
        
        @if($deliveryFee > 0)
        <div class="flex justify-between">
            <span class="text-gray-600 ">
                {{ __('messages.cart.delivery_fee') }}
            </span>
            <span class="font-bold">{{ number_format($deliveryFee) }} Ks</span>
        </div>
        @endif
        
        <hr>
        
        <div class="flex justify-between text-lg font-bold">
            <span class="">
                {{ __('messages.cart.total') }}
            </span>
            <span>{{ number_format($this->total) }} Ks</span>
        </div>

        {{-- show user can earn points --}}
        @if($this->pointsToEarn > 0)
        <div class="bg-gradient-to-r from-slate-50 to-emerald-50 border border-slate-200 rounded-lg p-4 animate-fade-in">
            <div class="flex items-center space-x-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm mb-2 font-medium text-slate-800 ">
                                {{ __('messages.cart.earn_points_title') }}
                            </p>
                            <p class="text-xs text-slate-600 ">
                                {{ __('messages.cart.earn_points_subtitle') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-1 bg-slate-100 px-3 py-1 rounded-full">
                            <span class="text-lg font-bold text-slate-700">+{{ number_format($this->pointsToEarn) }}</span>
                            <span class="text-xs text-slate-600 ">{{ __('messages.cart.points') }}</span>
                        </div>
                    </div>
                    
                    @auth
                        <div class="mt-2 flex items-center space-x-4 text-xs text-slate-600">
                            <div class="flex items-center space-x-1">
                                <span class="">{{ __('messages.cart.current_balance') }}: {{ number_format(auth()->user()->points ?? 0) }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 ">
                                    path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                                <span class="">{{ __('messages.cart.after_order') }}: {{ number_format((auth()->user()->points ?? 0) + $this->pointsToEarn) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="mt-2">
                            <a href="{{ route('login') }}" class="text-xs text-slate-700 hover:text-slate-800 underline ">
                                {{ __('messages.cart.login_to_earn_points') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
