<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 ">{{ __('messages.content.about_us_title') }}</h1>
        
        @if($content)
            <div class="prose prose-lg max-w-none ">
                {!! $content !!}
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-3 ">{{ __('messages.content.not_available_title', ['type' => __('messages.content.about_us_title')]) }}</h3>
                <p class="text-gray-600 max-w-md mx-auto ">
                    {{ __('messages.content.not_available_message', ['type' => strtolower(__('messages.content.about_us_title'))]) }}
                </p>
            </div>
        @endif
    </div>
</div>
