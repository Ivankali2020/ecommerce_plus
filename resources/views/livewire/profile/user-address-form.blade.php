<div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Address Table -->
    <div class="">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.address.my_addresses') }}
            </h3>
            <span class="text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
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
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $address->address }}</p>
                                    <div class="mt-1 flex items-center text-xs text-gray-500">
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
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.no_addresses') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.start_message') }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    @if(count($addresses) < 4)
        <!-- Address Form -->
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ $editId ? __('messages.address.edit') : __('messages.address.add_new') }}
            </h3>

            <div class="space-y-4">
                <div>
                    <label for="region" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.region') }}
                    </label>
                    <select id="region" wire:model.live="selectedRegion"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm rounded-md {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        <option value="">{{ __('messages.address.select_region') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->mm_name }}</option>
                        @endforeach
                    </select>
                    @error('selectedRegion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="township" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.township') }}
                    </label>
                    <select id="township" wire:model.live="selectedTownship"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm rounded-md {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        <option value="">{{ __('messages.address.select_township') }}</option>
                        @foreach($townships as $township)
                            <option value="{{ $township->id }}">{{ $township->mm_name }}</option>
                        @endforeach
                    </select>
                    @error('selectedTownship')
                        <p class="mt-1 text-sm text-red-600">{{ $selectedTownship }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.address_label') }}
                    </label>
                    <textarea id="address" wire:model="address" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-500 focus:border-slate-500 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                              placeholder="{{ __('messages.address.address_placeholder') }}"></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $address }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.address.phone') }}
                    </label>
                    <input type="text" id="phone" wire:model="phone"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-500 focus:border-slate-500 sm:text-sm {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"
                           placeholder="{{ __('messages.address.phone_placeholder') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $phone }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    @if($editId)
                        <button wire:click="cancelEdit" type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.address.cancel') }}
                        </button>
                    @endif
                    <button wire:click="save"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ $editId ? __('messages.address.update') : __('messages.address.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

