<div class="container mx-auto px-4 py-4 lg:py-8 mb-20 lg:mb-0">
    <h2 class="text-xl lg:text-3xl mb-4 lg:mb-10 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
        {{ __('messages.delivery.title') }}
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Region Selection -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.delivery.regions') }}
                </h3>
                
                @if($regions && $regions->count() > 0)
                    <div class="space-y-2">
                        @foreach($regions as $region)
                            <button 
                                wire:click="selectRegion({{ $region->id }})"
                                class="w-full text-left p-3 rounded-md transition-colors {{ $selectedRegion == $region->id ? 'bg-slate-500 text-white' : 'bg-gray-50 hover:bg-gray-100' }} {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                            >
                                {{ app()->currentLocale() === 'my' ? $region->mm_name : $region->en_name }}
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.delivery.no_delivery_areas') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Township Delivery Fees -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.delivery.townships_fees') }}
                </h3>

                @if($selectedRegion && $townships && $townships->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.delivery.township') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.delivery.delivery_fee') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.delivery.cod_available') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                        {{ __('messages.delivery.pickup_available') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($townships as $township)
                                    @if($township->deliveries && $township->deliveries->count() > 0)
                                        @foreach($township->deliveries as $delivery)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                                    {{ app()->currentLocale() === 'my' ? $township->mm_name : $township->en_name }}
                                                    <p class="text-red-700 text-xs "> {{  $delivery->remark }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 font-medium font-mono ">
                                                    {{ number_format($delivery->fees) }} {{ __('messages.currency') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($delivery->is_cod)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('messages.delivery.available') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ __('messages.delivery.not_available') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($delivery->is_pick_up)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ __('messages.delivery.available') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ __('messages.delivery.not_available') }}
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
                    <div class="text-center py-8 text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.delivery.no_townships') }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
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
                            <h3 class="text-sm font-medium text-slate-800 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.delivery.note_title') }}
                            </h3>
                            <div class="mt-2 text-sm text-slate-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
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
