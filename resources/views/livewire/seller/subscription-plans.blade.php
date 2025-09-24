<div class="pb-12 min-h-screen transition-colors duration-300">

    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
        aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#80ceff] to-[#bdbcc9] dark:from-slate-400 dark:via-[#c2a03d] dark:to-slate-400 opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <a href="/">
            <img class="mx-auto h-20 w-auto mb-4 " src="{{ asset('assets/logo.png') }}" alt="Your Logo">
        </a>
    
     
        @if($showPaymentForm && $selectedPlan)
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('subscription.choose_payment') }}</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">{{ __('subscription.select_payment_subtitle') }}</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8 transition-colors duration-300">
                <div class="flex items-center mb-6">
                    <button wire:click="$set('showPaymentForm', false)" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ __('subscription.payment_for', ['plan' => $selectedPlan->name]) }}</h2>
                </div>
                
                <div class="bg-slate-50 dark:bg-gray-700 p-4 rounded-lg mb-6 transition-colors duration-300">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ __('subscription.plan') }}:</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $selectedPlan->name }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ __('subscription.duration') }}:</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $selectedPlan->duration_months }} {{ __('subscription.months') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ __('subscription.price') }}:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($selectedPlan->price) }} MMK</span>
                    </div>
                </div>
                
                <form wire:submit.prevent="submitPayment">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-600 dark:bg-slate-700 border border-transparent rounded-md font-semibold text-white hover:bg-slate-700 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            <svg wire:loading wire:target="submitPayment" class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2.93 6.364A8.003 8.003 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3.93-1.574z"></path>
                            </svg>
                            {{ __('subscription.submit_payment') }}
                        </button>
                    </div>
                </form>
            </div>
        @elseif(!$showThankYouPage)
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('subscription.choose_subscription_plan') }}</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">{{ __('subscription.select_plan_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl dark:hover:shadow-2xl dark:hover:shadow-gray-900/50 transition-all duration-300">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h2>
                            <div class="text-3xl font-bold text-slate-600 dark:text-slate-400 mb-4">
                                {{ number_format($plan->price) }} MMK
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ __('subscription.per_month', ['months' => $plan->duration_months]) }}</span>
                            </div>
                            
                            <div class="border-t border-gray-200 dark:border-gray-600 my-4 pt-4">
                                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $plan->description }}</p>
                                
                                <ul class="space-y-3 mb-6 font-mm_lat">
                                    @foreach(explode(', ', $plan->features) as $feature)
                                        <li class="flex items-start">
                                            <svg class="h-5 w-5 text-green-500 dark:text-green-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">{{ trim($feature) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <button 
                                wire:click="selectPlan({{ $plan->id }})"
                                class="w-full bg-slate-600 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200">
                                {{ __('subscription.select_plan') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <!-- Add this section for the Thank You page -->
        @if($showThankYouPage)
            <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-8 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('subscription.thank_you_title') }}</h2>
                    
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        {{ __('subscription.payment_pending_message') }}
                    </p>
                    
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        {{ __('subscription.approval_notification') }}
                    </p>

                    {{-- contact --}}
                    <p class="text-gray-600 text-xl font-bebas dark:text-gray-300 mb-8">
                      CONTACT US : 09453183072
                    </p>
                    
                    <div class="mt-6">
                        <a href="{{ route('filament.seller.pages.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 dark:bg-primary-700 border border-transparent rounded-md font-semibold text-white hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            {{ __('subscription.go_to_dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
        aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr  from-[#80ceff] to-[#bdbcc9] dark:from-slate-400 dark:via-[#c2a03d] dark:to-slate-400 opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>
    </div>
   
</div>