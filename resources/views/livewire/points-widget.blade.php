<div class="rounded-lg shadow-sm border border-gray-200 p-6">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                <x-primary-link-button :href="route('gifts')" >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"></path>
                    </svg>
                   {{ __('gifts.points_widget.exchange_gifts') }}
                </x-primary-link-button>

                <x-primary-link-button :href="route('products')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"></path>
                    </svg>
                    {{ __('gifts.points_widget.shop_earn_points') }}
                </x-primary-link-button>
            </div>
        </div>

    @else
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('gifts.points_widget.login_required_title') }}</h3>
            <p class="text-gray-600 mb-4">{{ __('gifts.points_widget.login_required_subtitle') }}</p>
            <a href="{{ route('login') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                {{ __('gifts.login_now') }}
            </a>
        </div>
    @endif
</div>
