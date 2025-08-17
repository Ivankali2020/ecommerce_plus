<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 ">{{ __('messages.content.terms_conditions_title') }}</h1>
        
        @if($content)
            <div class="prose prose-lg max-w-none ">
                {!! $content !!}
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-3 ">{{ __('messages.content.not_available_title', ['type' => __('messages.content.terms_conditions_title')]) }}</h3>
                <p class="text-gray-600 max-w-md mx-auto ">
                    {{ __('messages.content.not_available_message', ['type' => strtolower(__('messages.content.terms_conditions_title'))]) }}
                </p>
            </div>
        @endif
    </div>
</div>
