<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 ">{{ __('messages.content.faq_title') }}</h1>
        <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchTerm"
                        placeholder="{{ __('messages.content.search_faq_placeholder') }}" 
                        class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl transition-colors duration-200 focus:ring-0 focus:border-slate-500 focus:outline-0 "
                    >
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        @if(count($this->filteredFaqs) > 0)
            

            <!-- FAQ Items -->
            <div class="space-y-4">
                @foreach($this->filteredFaqs as $index => $faqItem)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">
                        <button 
                            wire:click="toggleFaq({{ $index }})"
                            class="w-full px-6 py-5 text-left font-semibold text-gray-800 hover:bg-gray-50 transition-colors duration-200 flex justify-between items-center "
                            aria-expanded="{{ $openFaq === $index ? 'true' : 'false' }}"
                        >
                            <span class="text-lg">{{ $faqItem['question'] }}</span>
                            <div class="shrink-0 ml-4">
                                <svg 
                                    class="w-6 h-6 text-gray-500 transform transition-transform duration-300 ease-in-out {{ $openFaq === $index ? 'rotate-180' : 'rotate-0' }}" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        
                        <div 
                            class="overflow-hidden transition-all duration-300 ease-in-out {{ $openFaq === $index ? 'max-h-screen opacity-100' : 'max-h-0 opacity-0' }}"
                        >
                            <div class="px-6 py-5 border-t border-gray-100 bg-gray-50/50">
                                <div class="prose prose-sm max-w-none ">
                                    {!! $faqItem['answer'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($searchTerm && count($this->filteredFaqs) === 0)
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center mt-8">
                    <div class="text-gray-500 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2 ">{{ __('messages.content.no_results_found') }}</h3>
                    <p class="text-gray-600 mb-4 ">
                        {{ __('messages.content.no_results_message', ['term' => $searchTerm]) }}
                    </p>
                    <button 
                        wire:click="$set('searchTerm', '')" 
                        class="inline-flex items-center px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors duration-200 "
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ __('messages.content.clear_search') }}
                    </button>
                </div>
            @endif
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-3 ">{{ __('messages.content.not_available_title', ['type' => __('messages.content.faq_title')]) }}</h3>
                <p class="text-gray-600 max-w-md mx-auto ">
                    {{ __('messages.content.faq_not_available_message') }}
                </p>
            </div>
        @endif
    </div>
</div>
