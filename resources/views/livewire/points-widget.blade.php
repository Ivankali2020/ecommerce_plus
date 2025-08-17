<div class="rounded-lg shadow-sm border border-gray-200 p-6 ">
    @if($user)
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-slate-500 to-slate-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('gifts.points_widget.title') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('gifts.points_widget.subtitle') }}</p>
                </div>
            </div>
            <button wire:click="toggleHistory" 
                    class="text-slate-600 hover:text-slate-800 text-sm font-medium">
                {{ $showHistory ? __('gifts.points_widget.hide_history') : __('gifts.points_widget.view_history') }}
            </button>
        </div>

        @if($pointsSummary)
            <div class="grid grid-cols-2  lg:grid-cols-4 gap-4 mb-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($pointsSummary['current_balance']) }}</div>
                    <div class="text-sm text-gray-600">{{ __('gifts.points_widget.current_balance') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($pointsSummary['total_earned']) }}</div>
                    <div class="text-sm text-gray-600">{{ __('gifts.points_widget.total_earned') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($pointsSummary['total_spent']) }}</div>
                    <div class="text-sm text-gray-600">{{ __('gifts.points_widget.total_spent') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($pointsSummary['transaction_count']) }}</div>
                    <div class="text-sm text-gray-600">{{ __('gifts.points_widget.transactions') }}</div>
                </div>
            </div>
        @endif

        @if($showHistory && $recentTransactions->isNotEmpty())
            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-3">{{ __('gifts.points_widget.recent_transactions') }}</h4>
                <div class="space-y-2">
                    @foreach($recentTransactions as $transaction)
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type === 'earned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ __('gifts.points_widget.transaction_types.' . $transaction->type) }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ $transaction->description }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $transaction->created_at->format('M d, Y \a\t H:i') }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium {{ $transaction->type === 'earned' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'earned' ? '+' : '' }}{{ number_format($transaction->points) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ __('gifts.points_widget.balance') }}: {{ number_format($transaction->balance_after) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="border-t pt-4 mt-4">
            <div class="flex flex-wrap gap-2">
                <x-primary-link-button :href="route('gifts')"  class="w-full md:w-auto">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-2 " fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                   {{ __('gifts.points_widget.exchange_gifts') }}
                </x-primary-link-button>

                <x-primary-link-button :href="route('products')" class="w-full md:w-auto">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-2 "  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    {{ __('gifts.points_widget.shop_earn_points') }}
                </x-primary-link-button>

                <x-secondary-link-button :href="route('gift.exchange.history')" class="w-full md:w-auto">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                   {{ __('gifts.points_widget.view_gift_history') }}
                </x-secondary-link-button>

                <x-secondary-link-button :href="route('point.transaction.history')" class="w-full md:w-auto">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0-1.5 1.5M21 21l-1.5-1.5m0 0h-5.25m5.25 0H9.75m0 0L3 15" />
                    </svg>
                   {{ __('gifts.points_widget.view_point_history') }}
                </x-secondary-link-button>
            </div>
        </div>

    @else
        <div class="text-center py-3 lg:py-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <img src="{{ asset('assets/profile.png') }}" alt="Points" class="w-12 h-12">
            </div>
            <h3 class="text-sm lg:text-2xl font-medium text-gray-900 mb-2">{{ __('gifts.points_widget.login_required_title') }}</h3>
            <p class="text-gray-600 text-xs lg:text-lg mb-4">{{ __('gifts.points_widget.login_required_subtitle') }}</p>
            <x-primary-link-button :href="route('register')" class="mt-2">
                 {{ __('gifts.login_now') }}
            </x-primary-link-button>
        </div>
    @endif
</div>
