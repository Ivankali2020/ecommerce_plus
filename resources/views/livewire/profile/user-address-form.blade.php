
<div class="max-w-7xl mx-auto px-4 lg:px-8  space-y-6">
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
       
        <div>
                <!-- Address Table -->
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 ">
                    {{ __('messages.address.my_addresses') }}
                </h3>
                <span class="text-sm text-gray-500 ">
                    {{ count($addresses) }}/3 {{ __('messages.address.addresses_count') }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($addresses as $address)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4">
                        <div class="flex justify-between">
                            <div class="space-y-2 flex-1">
                                <!-- Phone and Actions -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $address->phone }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="edit({{ $address->id }})" class="p-1 text-slate-600 hover:text-slate-900 rounded-full hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $address->id }})" class="p-1 text-red-600 hover:text-red-900 rounded-full hover:bg-red-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Location Details -->
                                <div class="flex items-start space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="flex-1 ">
                                        <p class="text-sm text-gray-900">{{ $address->address }}</p>
                                        <div class="mt-1 flex items-center text-xs text-gray-500 ">
                                            <span>{{ $address->township->mm_name }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                            <span>{{ $address->region->mm_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-4  lg:p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 ">
                            {{ __('messages.address.no_addresses') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 ">
                            {{ __('messages.address.start_message') }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
   

        @if(count($addresses) < 4)
            <!-- Address Form -->
            <div class="lg:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 ">
                    {{ $editId ? __('messages.address.edit') : __('messages.address.add_new') }}
                </h3>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="region" :value="__('messages.address.region')" />
                        <x-select id="region" wire:model.live="selectedRegion" class="mt-1 block w-full">
                            <option value="">{{ __('messages.address.select_region') }}</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->mm_name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-2" :messages="$errors->get('selectedRegion')" />
                    </div>

                    <div>
                        <div class="flex gap-2 ">
                            <x-input-label for="township" :value="__('messages.address.township')" />
                            <x-filament::loading-indicator wire:loading class="h-4 w-4" />
                        </div>
                        <x-select id="township" wire:model.live="selectedTownship" wire:key="township-{{ $selectedRegion }}" class="mt-1 block w-full">
                            <option value="">{{ __('messages.address.select_township') }}</option>
                            @foreach($townships as $township)
                                <option wire:key="{{ $township->id}}" value="{{ (string)$township->id }}" 
                                        @if($selectedTownship == $township->id) selected @endif>
                                    {{ $township->mm_name }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-2" :messages="$errors->get('selectedTownship')" />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('messages.address.address_label')" />
                        <x-textarea id="address" wire:model="address" rows="3" class="mt-1 block w-full"
                                placeholder="{{ __('messages.address.address_placeholder') }}"></x-textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('messages.address.phone')" />
                        <x-text-input type="text" id="phone" wire:model="phone" class="mt-1 block w-full"
                            placeholder="{{ __('messages.address.phone_placeholder') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <div class="flex justify-end gap-3">
                        @if($editId)
                            <x-secondary-button wire:click="cancelEdit" type="button" wire:loading.attr="disabled">
                                {{ __('messages.address.cancel') }}
                            </x-secondary-button>
                        @endif

                        <x-primary-button wire:click="save" wire:loading.attr="disabled" type="button" class="group relative">
                            <span class="inset-y-0 flex items-center me-3">
                                <svg wire:loading.remove class="h-5 w-5 text-slate-500 group-hover:text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                <x-filament::loading-indicator wire:loading class="h-5 w-5 text-slate-500" />
                            </span>
                            <span wire:loading.remove>{{ $editId ? __('messages.address.update') : __('messages.address.save') }}</span>
                            <span wire:loading>{{ $editId ? __('messages.address.update') : __('messages.address.save') }}...</span>
                        </x-primary-button>
                    </div>
                </div>
            </div>
        @endif

     </div>
</div>