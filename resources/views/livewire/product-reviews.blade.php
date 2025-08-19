<div class="">
    <!-- Reviews Header -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-sm md:text-2xl font-bold text-gray-900 ">
                {{ __('messages.reviews.title') }}
            </h2>
            
            @auth
                @if(!$this->userHasReviewed)
                    <button 
                        wire:click="toggleReviewForm"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 transition-colors duration-200 "
                    >
                        @if($showReviewForm)
                            {{ __('messages.reviews.cancel_review') }}
                        @else
                            {{ __('messages.reviews.write_review') }}
                        @endif
                    </button>
                @endif
            @else
                <a 
                    href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-xs md:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 "
                >
                    {{ __('messages.reviews.login_to_review') }}
                </a>
            @endauth
        </div>

        <!-- Rating Summary -->
        @if($this->totalReviews > 0)
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Average Rating -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start space-x-2">
                        <span class="text-4xl font-bold text-gray-900">{{ number_format($this->averageRating, 1) }}</span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $this->averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 ">
                        {{ __('messages.reviews.based_on_reviews', ['count' => $this->totalReviews]) }}
                    </p>

                    <!-- User already reviewed notice -->
                    @if(auth()->check() && $this->userHasReviewed)
                        <div class="mt-4 bg-slate-50 border border-slate-200 rounded-md p-4">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-slate-800 ">
                                        {{ __('messages.reviews.already_reviewed') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Rating Breakdown -->
                <div class="space-y-2 ">
                    @foreach($this->ratingBreakdown as $stars => $data)
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-700 w-20 ">{{ $stars }} {{ __('messages.reviews.stars') }}</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12 text-right ">{{ $data['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Review Form -->
    @if($showReviewForm && auth()->check() && !$this->userHasReviewed)
        <div class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 ">
                {{ __('messages.reviews.write_your_review') }}
            </h3>
            
            <form wire:submit="submitReview" class="space-y-4">
                <!-- Rating Stars -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.reviews.rating') }}
                    </label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button 
                                type="button"
                                wire:click="$set('newRating', {{ $i }})"
                                class=""
                            >
                                <svg class="w-8 h-8 {{ $i <= $newRating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors duration-150" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600 ">
                            {{ $newRating }} {{ $newRating === 1 ? __('messages.reviews.star') : __('messages.reviews.stars') }}
                        </span>
                    </div>
                    @error('newRating')
                        <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2 ">
                        {{ __('messages.reviews.comment') }}
                    </label>
                    <x-textarea wire:model="newComment" id="comment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm " placeholder="{{ __('messages.reviews.comment_placeholder') }}" />
                    @error('newComment')
                        <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        wire:click="toggleReviewForm"
                        class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 "
                    >
                        {{ __('messages.reviews.cancel') }}
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 transition-colors duration-200 "
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ __('messages.reviews.submit') }}</span>
                        <span wire:loading>{{ __('messages.reviews.submitting') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                    <div class="flex items-start space-x-4">
                        <!-- User Avatar -->
                        <div class="shrink-0">
                            @if($review->user->getFirstMediaUrl('profile_photo'))
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $review->user->getFirstMediaUrl('profile_photo') }}" alt="{{ $review->user->name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($review->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Review Content -->
                        <div class="flex-1 min-w-0">
                            <!-- User Info and Rating -->
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 ">
                                        {{ $review->user->name }}
                                    </h4>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->stars ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 ">
                                    {{ $review->created_at->format('d M Y H:i:s') }}
                                </p>
                            </div>

                            <!-- Review Comment -->
                            <div class="text-gray-700 text-sm leading-relaxed ">
                                {{ $review->comment }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @endif
    @else
        <!-- No Reviews State -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2 ">
                {{ __('messages.reviews.no_reviews') }}
            </h3>
            <p class="text-gray-500 ">
                {{ __('messages.reviews.be_first_to_review') }}
            </p>
        </div>
    @endif
</div>
