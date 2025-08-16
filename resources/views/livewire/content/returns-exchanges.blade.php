<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">{{ __('messages.content.returns_exchanges_title') }}</h1>
        
        @if($content)
            <div class="prose prose-lg max-w-none {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                {!! $content !!}
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-3 {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">{{ __('messages.content.not_available_title', ['type' => __('messages.content.returns_exchanges_title')]) }}</h3>
                <p class="text-gray-600 max-w-md mx-auto {{ app()->currentLocale() === 'my' ? 'font-mm' : '' }}">
                    {{ __('messages.content.not_available_message', ['type' => strtolower(__('messages.content.returns_exchanges_title'))]) }}
                </p>
            </div>
        @endif
    </div>
</div>
