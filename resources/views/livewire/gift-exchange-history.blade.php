<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 ">
                {{ __('messages.gift_exchange_history.title') }}
            </h1>
            <p class="text-gray-600 mt-2 ">
                {{ __('messages.gift_exchange_history.description') }}
            </p>
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
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-0 focus:border-black  focus:outline-none  "
                            placeholder="{{ __('messages.gift_exchange_history.search_placeholder') }}"
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.status') }}
                    </label>
                    <select 
                        wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-0 focus:border-black  focus:outline-none  "
                    >
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="pending">{{ __('messages.gift_exchange_history.status.pending') }}</option>
                        <option value="processing">{{ __('messages.gift_exchange_history.status.processing') }}</option>
                        <option value="shipped">{{ __('messages.gift_exchange_history.status.shipped') }}</option>
                        <option value="delivered">{{ __('messages.gift_exchange_history.status.delivered') }}</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    @if($search || $statusFilter)
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

        <!-- Exchange History List -->
        @if($this->giftExchanges->count() > 0)
            <div class="space-y-6">
                @foreach($this->giftExchanges as $exchange)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-gray-300">
                        <!-- Mobile Layout -->
                        <div class="block lg:hidden p-4">
                            <!-- Header with Status -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <!-- Gift Image -->
                                    @if($exchange->gift->getFirstMediaUrl('images'))
                                        <img 
                                            src="{{ $exchange->gift->getFirstMediaUrl('images') }}" 
                                            alt="{{ $exchange->gift->name }}"
                                            class="w-12 h-12 rounded-lg object-cover"
                                        >
                                    @else
                                        <div class="w-12 h-12 bg-linear-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-semibold text-gray-900  truncate">
                                            {{ $exchange->gift->name }}
                                        </h3>
                                        <p class="text-xs text-gray-500 ">
                                            {{ $exchange->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $statusColors[$exchange->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    {{ __('messages.gift_exchange_history.status.' . $exchange->status) }}
                                </span>
                            </div>
                            
                            <!-- Gift Description -->
                            @if($exchange->gift->description)
                                <p class="text-sm text-gray-600  mb-3 line-clamp-2">
                                    {{ $exchange->gift->description }}
                                </p>
                            @endif
                            
                            <!-- Points Spent -->
                            <div class="flex items-center justify-between py-2 px-3 bg-red-50 rounded-lg mb-3">
                                <span class="text-sm text-gray-700 ">
                                    {{ __('messages.gift_exchange_history.points_spent') }}
                                </span>
                                <span class="text-lg font-bold text-red-600">
                                    {{ number_format($exchange->points_spent) }} {{ __('messages.points') }}
                                </span>
                            </div>
                            
                            <!-- Delivery Address Mobile -->
                            @if($exchange->delivery_address)
                                <div class="p-3 bg-gray-50 rounded-lg mb-3">
                                    <p class="text-xs font-medium text-gray-700  mb-1">
                                        {{ __('messages.gift_exchange_history.delivery_address') }}:
                                    </p>
                                    <p class="text-sm text-gray-600 ">
                                        {{ $exchange->delivery_address }}
                                    </p>
                                </div>
                            @endif
                            
                            <!-- Timeline Mobile -->
                            @if($exchange->status !== 'pending')
                                <div class="space-y-2">
                                    @if($exchange->processed_at)
                                        <div class="flex items-center space-x-2 text-xs">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="text-gray-600 ">
                                                {{ __('messages.gift_exchange_history.processed') }} - {{ $exchange->processed_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($exchange->shipped_at)
                                        <div class="flex items-center space-x-2 text-xs">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                            <span class="text-gray-600 ">
                                                {{ __('messages.gift_exchange_history.shipped') }} - {{ $exchange->shipped_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($exchange->delivered_at)
                                        <div class="flex items-center space-x-2 text-xs">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-gray-600 ">
                                                {{ __('messages.gift_exchange_history.delivered') }} - {{ $exchange->delivered_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Desktop Layout -->
                        <div class="hidden lg:block p-6">
                            <div class="flex items-start justify-between">
                                <!-- Gift Info -->
                                <div class="flex-1">
                                    <div class="flex items-start space-x-4">
                                        <!-- Gift Image -->
                                        @if($exchange->gift->getFirstMediaUrl('images'))
                                            <img 
                                                src="{{ $exchange->gift->getFirstMediaUrl('images') }}" 
                                                alt="{{ $exchange->gift->name }}"
                                                class="w-20 h-20 rounded-xl object-cover shadow-sm"
                                            >
                                        @else
                                            <div class="w-20 h-20 bg-linear-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center shadow-sm">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Gift Details -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-semibold text-gray-900  mb-1">
                                                {{ $exchange->gift->name }}
                                            </h3>
                                            @if($exchange->gift->description)
                                                <p class="text-gray-600 text-sm  mb-3 line-clamp-2">
                                                    {{ $exchange->gift->description }}
                                                </p>
                                            @endif
                                            <div class="flex items-center space-x-6">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm font-medium text-red-600 ">
                                                        {{ number_format($exchange->points_spent) }} {{ __('messages.points') }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-500 ">
                                                        {{ $exchange->created_at->format('M j, Y g:i A') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge Desktop -->
                                <div class="ml-6 shrink-0">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $statusColors[$exchange->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        {{ __('messages.gift_exchange_history.status.' . $exchange->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Timeline Desktop -->
                            @if($exchange->status !== 'pending')
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center space-x-8 text-sm">
                                        @if($exchange->processed_at)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-sm"></div>
                                                <span class="text-gray-600 ">
                                                    {{ __('messages.gift_exchange_history.processed') }}: {{ $exchange->processed_at->format('M j, Y g:i A') }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($exchange->shipped_at)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 bg-purple-500 rounded-full shadow-sm"></div>
                                                <span class="text-gray-600 ">
                                                    {{ __('messages.gift_exchange_history.shipped') }}: {{ $exchange->shipped_at->format('M j, Y g:i A') }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($exchange->delivered_at)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 bg-green-500 rounded-full shadow-sm"></div>
                                                <span class="text-gray-600 ">
                                                    {{ __('messages.gift_exchange_history.delivered') }}: {{ $exchange->delivered_at->format('M j, Y g:i A') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex flex-col lg:flex-row items-center justify-start gap-4 ">
                                <!-- Delivery Address Desktop -->
                                @if($exchange->delivery_address)
                                    <div class="mt-4 p-4 bg-linear-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-4 h-4 text-gray-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div class="flex justify-start items-center ">
                                                <p class="text-sm font-medium text-gray-700 me-2 ">
                                                    {{ __('messages.gift_exchange_history.delivery_address') }}
                                                </p>
                                                
                                                <p class="text-sm font-bold text-gray-900 ">
                                                    {{ $exchange->delivery_address }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Notes Desktop -->
                                @if($exchange->notes)
                                    <div class="mt-4 p-4 bg-linear-to-r from-red-50 to-red-100 rounded-lg border border-red-200">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <div class="flex justify-start items-center ">
                                                <p class="text-sm font-medium text-red-700 ">
                                                    {{ __('messages.notes') }}
                                                </p>
                                                <p class="text-sm text-red-600 mt-1 ">
                                                    {{ $exchange->notes }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->giftExchanges->hasPages())
                <div class="mt-8">
                    {{ $this->giftExchanges->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-linear-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 ">
                        {{ __('messages.gift_exchange_history.no_exchanges') }}
                    </h3>
                    <p class="text-gray-600 max-w-md mx-auto ">
                        {{ __('messages.gift_exchange_history.no_exchanges_description') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200 ">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h0a2 2 0 012 2v0H8v0z"></path>
                            </svg>
                            {{ __('messages.dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
