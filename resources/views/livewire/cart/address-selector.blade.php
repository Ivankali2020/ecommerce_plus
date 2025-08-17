<div class="space-y-4">
    <label class="block text-sm font-medium text-gray-700 ">
        {{ __('messages.cart.select_address') }}
    </label>
    
    <div class="grid grid-cols-1 gap-4">
        @forelse ($this->addresses as $address)
            <label wire:key="address_{{ $address->id }}" class="block relative bg-white border rounded-lg shadow-sm p-4 cursor-pointer hover:border-slate-500 focus:outline-none transition-all"
                @class(['ring-2 ring-slate-500' => $selectedAddress == $address->id])>
                <div class="flex justify-between items-start gap-4">
                    <input type="radio" name="address" value="{{ $address->id }}"
                        wire:model.live="selectedAddress" class="text-slate-900 ring-0 focus:ring-0 mt-1">
                    <div class="flex-1" >
                        <div class="text-sm font-medium text-gray-900">{{ $address->address }}</div>
                        <div class="mt-1 text-sm text-gray-500">{{ $address->phone }}</div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ $address->township->mm_name }}, {{ $address->region->mm_name }}
                        </div>
                    </div>
                    @if($selectedAddress == $address->id)
                        <div wire:loading wire:target="selectedAddress" class="text-sm text-gray-500 absolute top-2 right-2">
                            <x-filament::loading-indicator class="h-5 w-5" />
                        </div>
                    @endif
                </div>
            </label>
        @empty
            <div class="flex flex-col relative justify-center items-center my-2 rounded border py-3">
                <a href="/profile" class="absolute w-full h-full"></a>
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-sm my-2 text-slate-600 ">
                    {{ __('messages.cart.no_addresses') }}
                </p>
                <p class="text-xs text-slate-400 ">
                    {{ __('messages.cart.create_new_address') }}
                </p>
            </div>
        @endforelse
    </div>

    <!-- Delivery Details for Selected Address -->
    @if($selectedAddress && $deliveryDetails)
    <div class="mt-4 p-4 rounded-lg border {{ isset($deliveryDetails['unavailable']) ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                @if(isset($deliveryDetails['unavailable']))
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="text-sm font-medium text-red-700 ">
                        {{ __('messages.cart.delivery_unavailable') }}
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="text-sm font-medium text-green-700 ">
                        {{ __('messages.cart.delivery_available') }}
                    </span>
                @endif
            </div>

            @unless(isset($deliveryDetails['unavailable']))
                <!-- Delivery Fee -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        <span class="text-sm text-gray-600 ">{{ __('messages.cart.delivery_fee') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-medium text-gray-900">{{ number_format($deliveryDetails['fees']) }}</span>
                        <span class="text-xs text-gray-500">MMK</span>
                    </div>
                </div>

                <!-- COD Availability -->
                @if($deliveryDetails['is_cod'])
                    <div class="flex items-center gap-2 p-2 bg-blue-50 rounded border border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <span class="text-sm text-blue-700 ">
                            {{ __('messages.cart.cod_available') }}
                        </span>
                    </div>
                @else
                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span class="text-sm text-gray-600 ">
                            {{ __('messages.cart.delivery_unavailable') }}
                        </span>
                    </div>
                @endif

                @if($deliveryDetails['remark'])
                    <p class="text-sm text-gray-600 italic">{{ $deliveryDetails['remark'] }}</p>
                @endif
            @else
              {{-- this is sub title  --}}
            @endunless
        </div>
    </div>
    @endif
    
    @error('selectedAddress')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
