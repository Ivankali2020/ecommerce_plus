<div class="min-h-screen  ">
    <!-- Header Section -->
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-2">{{ __('gifts.page_title') }}</h1>
                <p class="text-lg opacity-90">{{ __('gifts.page_subtitle') }}</p>
            </div>
            
            @auth
                <!-- User Points Summary -->
                <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-lg p-4 max-w-md mx-auto border ">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-75">{{ __('gifts.your_points_balance') }}</p>
                            <p class="text-2xl font-bold">{{ number_format($userPoints) }} {{ __('gifts.pts') }}</p>
                        </div>
                        <div class="text-right text-sm">
                            @if($pointsSummary)
                                <p>{{ __('gifts.earned') }}: {{ number_format($pointsSummary['total_earned']) }}</p>
                                <p>{{ __('gifts.spent') }}: {{ number_format($pointsSummary['total_spent']) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 text-center">
                    <p class="mb-4">{{ __('gifts.login_required') }}</p>
                    <a href="{{ route('login') }}" class="bg-white text-slate-600 px-6 py-2 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                        {{ __('gifts.login_now') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>


    <!-- Filters and Search -->
    <div class="max-w-7xl  mx-auto px-4 py-6">
        <!-- Gifts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($gifts as $gift)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <!-- Gift Image -->
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        @if($gift->image)
                            <img src="{{ asset('storage/' . $gift->image) }}" 
                                 alt="{{ $gift->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Gift Details -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 text-gray-800">{{ $gift->name }}</h3>
                        
                        @if($gift->description)
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $gift->description }}</p>
                        @endif

                        <!-- Points Required -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ number_format($gift->points_required) }} {{ __('gifts.pts') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $gift->stock_quantity }} {{ __('gifts.gift_card.left') }}
                            </div>
                        </div>

                        <!-- Additional Data -->
                        @if($gift->additional_data && count($gift->additional_data) > 0)
                            <div class="mb-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($gift->additional_data as $key => $value)
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                            {{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Exchange Button -->
                            @if($userPoints >= $gift->points_required)
                                <button wire:click="openExchangeModal({{ $gift->id }})" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                    {{ __('gifts.gift_card.exchange_now') }}
                                </button>
                            @else
                                <button disabled 
                                        class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg font-medium cursor-not-allowed">
                                    {{ __('gifts.gift_card.insufficient_points') }}
                                </button>
                            @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('gifts.no_gifts.title') }}</h3>
                    <p class="text-gray-500">{{ __('gifts.no_gifts.subtitle') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $gifts->links() }}
        </div>
    </div>

    <!-- Exchange Confirmation Modal -->
    @if($showExchangeModal && $selectedGift)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-2 lg:p-4 z-50">
            <div class="bg-white rounded-lg max-w-md w-full max-h-screen overflow-y-auto">
                <div class="p-3 md:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ __('gifts.exchange_modal.title') }}</h3>
                        <button wire:click="closeExchangeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Gift Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-medium mb-2">{{ $selectedGift->name }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ $selectedGift->description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium">{{ number_format($selectedGift->points_required) }} {{ __('gifts.pts') }}</span>
                            <span class="text-sm text-gray-500">{{ $selectedGift->stock_quantity }} {{ __('gifts.exchange_modal.available') }}</span>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('gifts.exchange_modal.delivery_address') }}
                        </label>
                        <textarea wire:model="deliveryAddress" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent"
                                  placeholder="{{ __('gifts.exchange_modal.delivery_address_placeholder') }}"></textarea>
                        @error('deliveryAddress') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('gifts.exchange_modal.additional_notes') }}
                        </label>
                        <textarea wire:model="exchangeNotes" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent"
                                  placeholder="{{ __('gifts.exchange_modal.notes_placeholder') }}"></textarea>
                        @error('exchangeNotes') 
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Points Summary -->
                    <div class="bg-slate-50 rounded-lg p-3 mb-6">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ __('gifts.exchange_modal.points_summary.current_points') }}</span>
                            <span class="font-medium">{{ number_format($userPoints) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ __('gifts.exchange_modal.points_summary.points_required') }}</span>
                            <span class="font-medium text-red-600">-{{ number_format($selectedGift->points_required) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-medium">
                            <span>{{ __('gifts.exchange_modal.points_summary.remaining_points') }}</span>
                            <span class="text-slate-600">{{ number_format($userPoints - $selectedGift->points_required) }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button wire:click="closeExchangeModal" 
                                class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            {{ __('gifts.exchange_modal.cancel') }}
                        </button>
                        <button wire:click="confirmExchange" 
                                class="flex-1 py-2 px-4 bg-slate-600 hover:bg-slate-700 text-white rounded-lg font-medium transition-colors">
                            {{ __('gifts.exchange_modal.confirm_exchange') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
