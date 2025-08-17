<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 ">
                {{ __('messages.point_transaction_history.title') }}
            </h1>
            <p class="text-gray-600 mt-2 ">
                {{ __('messages.point_transaction_history.description') }}
            </p>
        </div>

        <!-- Points Summary -->
        <div class="bg-white hidden lg:block rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($this->currentBalance) }}</div>
                    <div class="text-sm text-gray-600 ">{{ __('messages.point_transaction_history.current_balance') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($this->totalEarned) }}</div>
                    <div class="text-sm text-gray-600 ">{{ __('messages.point_transaction_history.total_earned') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-600">{{ number_format($this->totalSpent) }}</div>
                    <div class="text-sm text-gray-600 ">{{ __('messages.point_transaction_history.total_spent') }}</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.search') }}
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-black  focus:ring-0 focus:outline-none "
                            placeholder="{{ __('messages.point_transaction_history.search_placeholder') }}"
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.point_transaction_history.transaction_type') }}
                    </label>
                    <select 
                        wire:model.live="typeFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-0 focus:border-black  focus:outline-none "
                    >
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="earned">{{ __('messages.point_transaction_history.type.earned') }}</option>
                        <option value="spent">{{ __('messages.point_transaction_history.type.spent') }}</option>
                        <option value="expired">{{ __('messages.point_transaction_history.type.expired') }}</option>
                        <option value="refunded">{{ __('messages.point_transaction_history.type.refunded') }}</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    @if($search || $typeFilter)
                        <button 
                            wire:click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 "
                        >
                            {{ __('messages.clear_filters') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        @if($this->pointTransactions->count() > 0)
            <div class="space-y-4">
                @foreach($this->pointTransactions as $transaction)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
                        <!-- Mobile Layout (Stack Vertically) -->
                        <div class="block md:hidden">
                            <!-- Header with Badge and Points -->
                            <div class="flex items-center justify-between mb-3">
                                @php
                                    $typeColors = [
                                        'earned' => 'bg-green-100 text-green-800',
                                        'spent' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                        'refunded' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $typeIcons = [
                                        'earned' => '↗',
                                        'spent' => '↙',
                                        'expired' => '⏰',
                                        'refunded' => '↩',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800' }}">
                                    <span class="mr-1">{{ $typeIcons[$transaction->type] ?? '•' }}</span>
                                    {{ __('messages.point_transaction_history.type.' . $transaction->type) }}
                                </span>
                                
                                <div class="text-right">
                                    @if($transaction->type === 'earned')
                                        <span class="text-xl font-bold text-green-600">+{{ number_format($transaction->points) }}</span>
                                    @elseif($transaction->type === 'spent')
                                        <span class="text-xl font-bold text-red-600">-{{ number_format(abs($transaction->points)) }}</span>
                                    @else
                                        <span class="text-xl font-bold text-gray-600">{{ number_format($transaction->points) }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Transaction Description -->
                            <div class="mb-2">
                                <h4 class="text-base font-medium text-gray-900  leading-tight">
                                    {{ $transaction->description ?: __('messages.point_transaction_history.no_description') }}
                                </h4>
                            </div>
                            
                            <!-- Footer Info -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 ">
                                    {{ $transaction->created_at->format('M j, Y') }}
                                </span>
                                <span class="text-gray-500 ">
                                    {{ __('messages.point_transaction_history.balance_after') }}: {{ number_format($transaction->balance_after) }}
                                </span>
                            </div>
                            
                            @if($transaction->expires_at)
                                <div class="mt-2 text-xs text-gray-400 ">
                                    @if($transaction->expires_at->isPast())
                                        <span class="text-red-600 font-medium">{{ __('messages.expired') }}</span>
                                    @else
                                        {{ __('messages.point_transaction_history.expires_at') }}: {{ $transaction->expires_at->format('M j, Y') }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Desktop Layout (Horizontal) -->
                        <div class="hidden md:flex md:items-center md:justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Transaction Type Badge -->
                                @php
                                    $typeColors = [
                                        'earned' => 'bg-green-100 text-green-800',
                                        'spent' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                        'refunded' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $typeIcons = [
                                        'earned' => '↗',
                                        'spent' => '↙',
                                        'expired' => '⏰',
                                        'refunded' => '↩',
                                    ];
                                @endphp
                                <div class="shrink-0">
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium {{ $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800' }}">
                                        <span class="mr-2 text-base">{{ $typeIcons[$transaction->type] ?? '•' }}</span>
                                        {{ __('messages.point_transaction_history.type.' . $transaction->type) }}
                                    </span>
                                </div>
                                
                                <!-- Transaction Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="mb-1">
                                        <h4 class="text-lg font-medium text-gray-900  truncate">
                                            {{ $transaction->description ?: __('messages.point_transaction_history.no_description') }}
                                        </h4>
                                    </div>
                                    <p class="text-sm text-gray-500 ">
                                        {{ $transaction->created_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Points and Balance -->
                            <div class="text-right shrink-0 ml-4">
                                <div class="mb-1">
                                    @if($transaction->type === 'earned')
                                        <span class="text-2xl font-bold text-green-600">+{{ number_format($transaction->points) }}</span>
                                    @elseif($transaction->type === 'spent')
                                        <span class="text-2xl font-bold text-red-600">-{{ number_format(abs($transaction->points)) }}</span>
                                    @else
                                        <span class="text-2xl font-bold text-gray-600">{{ number_format($transaction->points) }}</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 ">
                                    {{ __('messages.point_transaction_history.balance_after') }}: {{ number_format($transaction->balance_after) }}
                                </div>
                                @if($transaction->expires_at)
                                    <div class="text-xs text-gray-400 mt-1 ">
                                        @if($transaction->expires_at->isPast())
                                            <span class="text-red-600 font-medium">{{ __('messages.expired') }}</span>
                                        @else
                                            {{ __('messages.point_transaction_history.expires_at') }}: {{ $transaction->expires_at->format('M j, Y') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->pointTransactions->hasPages())
                <div class="mt-6">
                    {{ $this->pointTransactions->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2 ">
                    {{ __('messages.point_transaction_history.no_transactions') }}
                </h3>
                <p class="text-gray-600 ">
                    {{ __('messages.point_transaction_history.no_transactions_description') }}
                </p>
            </div>
        @endif
    </div>
</div>
