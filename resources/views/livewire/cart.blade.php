<div class="container mb-20 lg:mb-0  mx-auto px-0 sm:px-4 py-4 sm:py-8" >

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
        <!-- Cart Items -->
        <div class="w-full p-4 lg:p-0  lg:w-2/3">
            <h2 class="text-3xl font-bebas mb-6 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {{ __('messages.cart.shopping_cart') }}
            </h2>

            @forelse($cartItems as $item)
                <div wire:key="cart-item-{{ $item->id }}"
                    class="flex flex-col sm:flex-row items-start relative  {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }} sm:items-center gap-2 sm:gap-4 p-3 sm:p-4 mb-4 bg-white rounded-lg shadow-sm animate-fade-in-up">
                    <!-- Product Image -->
                    <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                    class="w-20 h-20 sm:w-24 sm:h-24 hidden lg:block  object-cover rounded-lg">

                    <!-- Product Details -->
                    <div class="flex-grow w-full sm:w-auto">
                        <h3 class="text-base sm:text-lg font-bold line-clamp-2">{{ $item->product->name }}</h3>
                        <p class="text-sm mb-1 text-gray-500" style="line-height: 1.5rem">
                            {{ $item->product->category?->name }}</p>

                        @if ($item->variant)
                            <p class="text-sm mb-1 text-gray-600">Variant: {{ $item->variant->name }}</p>
                            <dl class="space-y-2  text-xs ">
                                @foreach ($item->variant->attributeValues as $attributeValue)
                                    <div class="flex gap-4">
                                        @foreach ($attributeValue['value'] as $value)
                                            <dd
                                                class="text-sm inline  border rounded px-2 bg-slate-50  text-gray-900 col-span-2">
                                                {{ $value }}
                                            </dd>
                                        @endforeach
                                    </div>
                                @endforeach
                            </dl>
                        @else
                            <dl class="space-y-2  text-xs">
                                @foreach ($item->product->attributeValues as $attributeValue)
                                    <div class="flex gap-4">
                                        @foreach ($attributeValue['value'] as $value)
                                            <dd
                                                class="text-sm inline  border rounded px-2 bg-slate-50  text-gray-900 col-span-2">
                                                {{ $value }}
                                            </dd>
                                        @endforeach
                                    </div>
                                @endforeach
                            </dl>
                        @endif

                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex justify-between items-center w-full sm:w-auto mt-2 sm:mt-0">
                        <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                <path d="M3.75 7.25a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z" />
                              </svg>
                        </button>
                        <span class="w-12 text-center">{{ $item->quantity }}</span>
                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                              </svg>
                        </button>
                        </div>
                         <!-- Price - stack on mobile -->
                         <div class="text-right min-w-[100px] sm:min-w-40">
                            <!-- Price -->
                            <div class="text-right min-w-40 ">
                                <p class="text-xs text-slate-300 ">
                                    {{ $item->quantity }} x 
                                    @if ($item->variant)
                                    {{ number_format($item->variant->mmk_discount_price) }} Ks / pcs
                                    @else
                                    {{ number_format($item->product->mmk_discount_price) }} Ks / pcs
                                    @endif
                                </p>
                                <p class="font-bold my-1 ">
                                    {{ number_format($item->price * $item->quantity) }} Ks
                                </p>
                                @if ($item->variant && $item->variant->discount > 0)
                                    <p class="text-sm text-gray-400 ">
                                        <span class="line-through">
                                            {{ number_format($item->variant->mmk_price * $item->quantity) }} ks
                                        </span>
                                        <span class="text-xs ms-1 text-red-500">
                                            {{ $item->variant->discount }}% OFF
                                        </span>
                                    </p>

                                @endif
                            </div>
                        </div>
                    </div>

                    <button wire:click="removeItem({{ $item->id }})"
                        class="absolute top-2 right-2 sm:relative sm:top-auto sm:right-auto text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                </div>
            @empty
                <div class="text-center py-12 animate-fade-in">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.cart.empty_cart') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.cart.start_adding') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('store', $this->seller_id) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-0 focus:ring-offset-0  {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.cart.continue_shopping') }}
                        </a>
                    </div>
                </div>
            @endforelse

        </div>

        <!-- Order Summary -->
        @if ($cartItems->isNotEmpty())
            <div class="lg:w-1/3 font-jost " x-cloak x-data="deliveryHandler({{ json_encode($this->deliveries) }}, {{ json_encode($this->payments) }}, {{ $cartItems->sum(fn($item) => $item->total) }})" >
                <div class="bg-white p-4  lg:p-6 rounded-lg shadow-sm animate-fade-in">
                    <h2 class="text-2xl font-bebas mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                        {{ __('messages.cart.order_summary') }}
                    </h2>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.cart.subtotal') }}
                            </span>
                            <span class="font-bold" x-text="formatPrice(total) + ' ks'">   </span>
                        </div>
                        <div class="flex justify-between" x-show="deliveryDetails != null">
                            <span class="text-gray-600 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.cart.delivery_fee') }}
                            </span>
                            <span class="font-bold" x-text="deliveryDetails?.fees ?? 0"></span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span class="{{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                {{ __('messages.cart.total') }}
                            </span>
                            <span x-text="formatPrice(total + parseInt(deliveryDetails?.fees ?? 0)) + ' ks'">  </span>
                        </div>

                        <h2 class="text-2xl pt-4 font-bebas mb-4 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.cart.checkout_process') }}
                        </h2>

                        <div class="space-y-4">

                            <!-- Region Selection -->
                            <div class="space-y-2">
                                <label for="region" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                    {{ __('messages.cart.select_region') }}
                                </label>
                                <select x-model="selectedRegion" id="region"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm rounded-md ">
                                    <option value="">Select a region</option>
                                    <template x-for="region in deliveries" :key="region.region_name">
                                        <option :value="region.region_name" x-text="region.region_name"></option>
                                    </template>
                                </select>
                                @error('selectedRegion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Township Selection -->
                            <div class="space-y-2" x-show="selectedRegion">
                                <label class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                    {{ __('messages.cart.select_township') }}
                                </label>
                                <select x-model="selectedTownship"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm rounded-md ">
                                    <option value="">Select a township</option>
                                    <template x-for="township in getTownships()" :key="township.township_id">
                                        <option :value="township.township_id" x-text="township.name"></option>
                                    </template>
                                </select>
                                @error('selectedTownship')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delivery Details -->
                            <div x-show="deliveryDetails !== null" class="mt-4 p-4 bg-gray-50 rounded-md">
                                <div class="space-y-2">
                                    <template x-if="deliveryDetails">
                                        <div class="space-y-3 ">
                                                <!-- Delivery Fee -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                                          </svg>
                                                        <span class="text-sm text-gray-600" {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}> {{ __('messages.cart.delivery_fee') }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-sm font-medium text-gray-900" x-text="deliveryDetails ? formatPrice(deliveryDetails.fees) : '0'"></span>
                                                        <span class="text-xs text-gray-500">MMK</span>
                                                    </div>
                                                </div>

                                                <!-- Total Amount -->
                                                <div class="flex items-center justify-between ">
                                                    <div class="flex items-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                                          </svg>
                                                        <span class="text-sm font-medium text-slate-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}"> {{ __('messages.cart.total') }}</span>
                                                    </div>
                                                    <div class="flex items-baseline gap-1">
                                                        <span class="text-lg font-bold text-slate-900" x-text="formatPrice(total + parseInt(deliveryDetails?.fees ?? 0))"></span>
                                                        <span class="text-xs text-gray-500">MMK</span>
                                                    </div>
                                                </div>

                                            <template x-if="deliveryDetails.is_cod">
                                                <div class="flex items-center">
                                                    <input id="is_cod" x-model="is_cod" type="checkbox"
                                                        class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-0 ring-0 ">
                                                    <label for="is_cod" class="ml-2 block text-sm text-gray-900">
                                                        {{ __('messages.cart.cash_on_delivery') }}
                                                    </label>
                                                </div>
                                            </template>

                                            <template x-if="deliveryDetails.remark" class="mt-2">
                                                <p class="text-sm text-slate-400" x-text="deliveryDetails.remark"></p>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                            </div>

                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                    {{ __('messages.cart.select_address') }}
                                </label>
                                <div class="grid grid-cols-1 gap-4">
                                    @forelse ($addresses as $address)
                                        <label
                                            class="block bg-white border rounded-lg shadow-sm p-4 cursor-pointer hover:border-slate-500 focus:outline-none"
                                            :class="{ 'ring-2 ring-slate-500': {{ $address_id }} === {{ $address->id }} }">

                                            <div class="flex justify-between items-center gap-4  ">
                                                <input type="radio" name="address" value="{{ $address->id }}"
                                                    wire:model="address_id" class="text-slate-900 ring-0 focus:ring-0 ">
                                                <div class="text-sm text-gray-900 ">{{ $address->address }}</div>
                                            </div>
                                            <div class="mt-2 text-xs text-end  text-gray-500">
                                                <div class="text-sm text-gray-500">{{ $address->phone }}</div>
                                                {{ $address->township->mm_name }}, {{ $address->region->mm_name }} ,
                                            </div>
                                        </label>
                                        @empty
                                        <div class="flex flex-col relative justify-center items-center my-2 rounded border py-3 ">
                                            <a href="/profile" class="absolute w-full h-full "></a>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="text-sm my-2 text-slate-600 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                                {{ __('messages.cart.no_addresses') }}
                                            </p>
                                            <p class="text-xs text-slate-400 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                                {{ __('messages.cart.create_new_address') }}
                                            </p>
                                         </div>
                                    @endforelse
                                </div>
                                @error('address_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Methods (show only if not COD) -->
                            <div x-show="!is_cod">

                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <h3 class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                                            {{ __('messages.cart.payment_method') }}
                                        </h3>
                                        <template x-for="payment in payments" :key="payment.id">
                                            <div class="flex items-center">
                                                <input type="radio" :value="payment.id" x-model="selectedPayment" :id="'payment_' + payment.id"
                                                    class="mr-2 ring-none text-slate-800  focus:right-none focus:outline-none   cursor-pointer focus:ring-slate-50 ">
                                                <label :for="'payment_' + payment.id" class="  cursor-pointer"><span x-text="payment.name"></span></lable>
                                            </div>
                                        </template>
                                        @error('selectedPayment')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Payment Account Information -->
                                    <div x-show="selectedPayment" class="mt-4 p-4 bg-gray-50 rounded-md">
                                        <template x-if="paymentDetails">
                                            <template x-for="account in paymentDetails" :key="account.id">
                                                {{-- <div class="flex items-center gap-2  border rounded py-3 px-6 ">
                                                    <input :id="'account-' + account.id" type="radio"
                                                        :value="account.id" x-model="selectedPaymentAccount"
                                                        class="mr-2 text-slate-800 ring-none  focus:right-none focus:outline-none  focus:ring-slate-50 ">
                                                    <label :for="'account-' + account.id" class="space-y-2">
                                                        <p class="text-sm font-medium"> Name: <span
                                                                x-text="account.name"></span></p>
                                                        <p class="text-sm"> Number: <span class="font-bold"
                                                                x-text="account.number"></span></p>
                                                    </label>
                                                </div> --}}
                                                <div class="flex mb-1 items-center gap-2 border rounded-lg  py-4 px-6 bg-white hover:shadow-md transition-all">
                                                    <input :id="'account-' + account.id" type="radio" :value="account.id" x-model="selectedPaymentAccount"
                                                        class="mr-2 text-slate-800 ring-none focus:ring-none focus:outline-none focus:ring-slate-50 cursor-pointer ">
                                                    <label :for="'account-' + account.id" class="flex-grow ">
                                                        <p class="text-sm font-medium text-gray-900"
                                                            <span x-text="account.name"></span>
                                                        </p>
                                                        <div class="flex items-center justify-between">
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-bold" x-text="account.number"></span>
                                                            </p>
                                                            <button type="button"
                                                                x-data="{ copied: false }"
                                                                @click="
                                                                    navigator.clipboard.writeText(account.number);
                                                                    copied = true;
                                                                    setTimeout(() => copied = false, 1000);
                                                                "
                                                                class="ml-2 p-1.5 text-gray-500 hover:text-slate-700 rounded-md hover:bg-gray-100 transition-all">
                                                                <span x-show="!copied">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                                    </svg>
                                                                </span>
                                                                <span x-show="copied">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-500">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </label>
                                                </div>

                                            </template>
                                        </template>
                                        @error('selectedPaymentAccount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="paymentSlip" class="block text-sm font-medium text-gray-700 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">  {{ __('messages.cart.payment_slip') }}</label>
                                    <div x-data="{ fileName: '' }"
                                        class="relative border-2 mt-2  border-dashed border-gray-300 p-4 rounded-md cursor-pointer">
                                        <input type="file" wire:model="paymentSlip"
                                            @change="fileName = $event.target.files[0].name"
                                            class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                        <div class="text-center">
                                            <span x-text="fileName ? fileName : '{{ __('messages.cart.upload_message')}}'"
                                                class="text-gray-500"></span>
                                        </div>
                                    </div>

                                    @if ($paymentSlip)
                                        <img src="{{ $paymentSlip->temporaryUrl() }}" class="mt-2 w-full ">
                                    @endif
                                    @error('paymentSlip')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>

                            </div>


                        </div>
                        <button @click="handleCheckout"
                            class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                            {{ __('messages.cart.proceed_checkout') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>



<script>
    //  document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('alpine:init', () => {
        Alpine.data('deliveryHandler', function(deliveries, payments, total) {
            return {
                deliveries: deliveries,
                selectedRegion: '',
                selectedTownship: '',
                selectedDeliveryId: '',
                deliveryDetails: null,
                payments,
                selectedPayment: '',
                selectedPaymentAccount: '',
                paymentDetails: null,
                is_cod: false,
                total: total,

                getTownships() {
                    if (!this.selectedRegion) return [];
                    const region = this.deliveries.find(r => r.region_name === this.selectedRegion);
                    return region ? region.townships : [];
                },

                init() {
                    this.$nextTick(() => {
                        this.$watch('selectedTownship', (value) => {

                            if (value) {
                                const region = this.deliveries.find(r => r
                                    .region_name === this.selectedRegion);
                                if (region) {
                                    this.deliveryDetails = region.townships.find(t => t
                                        .township_id === parseInt(value));
                                        console.log(this.deliveryDetails);
                                    // Dispatch to Livewire
                                    // this.$wire.set('selectedRegion', this.deliveryDetails.region_id);
                                    // this.$wire.set('selectedTownship', this.deliveryDetails.township_id);
                                    // this.$wire.set('deliveryFee', this.deliveryDetails.fees);
                                }
                            } else {
                                this.deliveryDetails = null;
                            }
                        });

                        this.$watch('is_cod', (value) => {
                            this.isCod = value;
                        });

                        this.$watch('selectedPayment', (value) => {
                            if (value) {
                                const payment = this.payments.find(p => p.id ===
                                    parseInt(value));
                                if (payment) {
                                    this.paymentDetails = payment.accounts;
                                    // this.$wire.set('selectedPayment', payment.id);
                                }
                            } else {
                                this.paymentDetails = null;
                            }
                        });

                        this.deliveryDetails = this.$wire.deliveryDetails;
                        this.paymentDetails = this.$wire.paymentDetails;
                        this.selectedTownship = this.$wire.selectedTownship;
                        this.selectedRegion = this.$wire.selectedRegion;
                    });

                },

                formatPrice(price) {
                    return new Intl.NumberFormat('en-US').format(price);
                },


                handleCheckout() {
                    // Validate required fields
                    if (!this.selectedRegion || !this.selectedTownship) {
                        // alert('Please select region and township');
                        // return;
                    }

                    if(!this.is_cod){
                        if (!this.selectedPayment || !this.selectedPaymentAccount) {
                            // alert('Please select payment method and account');
                            // return;
                        }
                    }

                    if (!this.isCod) {
                         this.$wire.set('selectedPayment', this.selectedPayment);
                        this.$wire.set('payment_account_id', this.selectedPaymentAccount);
                        this.$wire.set('paymentDetails',this.paymentDetails);
                    }else{
                        this.$wire.set('is_cod', this.is_cod);
                    }


                    if(this.deliveryDetails){
                        this.$wire.set('delivery_id', this.deliveryDetails.delivery_id);
                        this.$wire.set('deliveryDetails',this.deliveryDetails);
                        this.$wire.set('selectedTownship',this.selectedTownship);
                        this.$wire.set('selectedRegion',this.selectedRegion);
                    }

                    this.$wire.checkout();
                },
            }
        });
    });

    document.addEventListener('livewire:load', () => {
        Alpine.initTree(document.querySelector('[x-data]'));
    });
    //  });
</script>
