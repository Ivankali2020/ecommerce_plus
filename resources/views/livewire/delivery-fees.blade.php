<div class="container mx-auto px-4 py-4 lg:py-8 mb-20 lg:mb-0">
    <h2 class="text-xl lg:text-3xl mb-4 lg:mb-10">
        {{ __('messages.delivery.title') }}
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Region Selection -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-4">
                    {{ __('messages.delivery.regions') }}
                </h3>
                
                @if($regions && $regions->count() > 0)
                    <div class="space-y-2">
                        @foreach($regions as $region)
                            <button 
                                wire:click="selectRegion({{ $region->id }})"
                                class="w-full text-left p-3 rounded-md transition-colors {{ $selectedRegion == $region->id ? 'bg-slate-500 text-white' : 'bg-gray-50 hover:bg-gray-100' }}"
                            >
                                {{ app()->currentLocale() === 'my' ? $region->mm_name : $region->en_name }}
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        {{ __('messages.delivery.no_delivery_areas') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Township Delivery Fees -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-4">
                    {{ __('messages.delivery.townships_fees') }}
                </h3>

                @if($selectedRegion && $townships && $townships->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                        {{ __('messages.delivery.township') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                        {{ __('messages.delivery.delivery_fee') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                        {{ __('messages.delivery.cod_available') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                        {{ __('messages.delivery.pickup_available') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($townships as $township)
                                    @if($township->deliveries && $township->deliveries->count() > 0)
                                        @foreach($township->deliveries as $delivery)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-900 text-nowrap">
                                                    {{ app()->currentLocale() === 'my' ? $township->mm_name : $township->en_name }}
                                                    <p class="text-red-700 text-xs "> {{  $delivery->remark }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 font-medium font-mono text-nowrap">
                                                    {{ number_format($delivery->fees) }} {{ __('messages.currency') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($delivery->is_cod)
                                                        <!-- Desktop view -->
                                                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('messages.delivery.available') }}
                                                        </span>
                                                        <!-- Mobile view with icon -->
                                                        <span class="sm:hidden inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100">
                                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <!-- Desktop view -->
                                                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ __('messages.delivery.not_available') }}
                                                        </span>
                                                        <!-- Mobile view with icon -->
                                                        <span class="sm:hidden inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100">
                                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($delivery->is_pick_up)
                                                        <!-- Desktop view -->
                                                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('messages.delivery.available') }}
                                                        </span>
                                                        <!-- Mobile view with icon -->
                                                        <span class="sm:hidden inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100">
                                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <!-- Desktop view -->
                                                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ __('messages.delivery.not_available') }}
                                                        </span>
                                                        <!-- Mobile view with icon -->
                                                        <span class="sm:hidden inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100">
                                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($selectedRegion)
                    <div class="text-center py-8 text-gray-500">
                        {{ __('messages.delivery.no_townships') }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        {{ __('messages.delivery.select_region_first') }}
                    </div>
                @endif
            </div>

            @if($selectedRegion && $townships && $townships->count() > 0)
                <div class="mt-4 bg-slate-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-slate-800">
                                {{ __('messages.delivery.note_title') }}
                            </h3>
                            <div class="mt-2 text-sm text-slate-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>{{ __('messages.delivery.note_1') }}</li>
                                    <li>{{ __('messages.delivery.note_2') }}</li>
                                    <li>{{ __('messages.delivery.note_3') }}</li>   
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
