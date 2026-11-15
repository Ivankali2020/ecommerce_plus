<div>
    {{-- Header --}}
    <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
        <h2 class="font-display text-3xl font-medium text-ink md:text-4xl">{{ __('messages.reviews.title') }}</h2>

        @auth
            @unless ($this->userHasReviewed)
                <x-shop.button :variant="$showReviewForm ? 'secondary' : 'primary'" size="sm" wire:click="toggleReviewForm">
                    {{ $showReviewForm ? __('messages.reviews.cancel_review') : __('messages.reviews.write_review') }}
                </x-shop.button>
            @endunless
        @else
            <x-shop.button variant="secondary" size="sm" :href="route('login')">{{ __('messages.reviews.login_to_review') }}</x-shop.button>
        @endauth
    </div>

    <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">

        {{-- Summary --}}
        @if ($this->totalReviews > 0)
            <aside class="space-y-6 lg:col-span-4">
                <div class="flex items-end gap-4">
                    <span class="font-display text-6xl leading-none text-ink">{{ number_format($this->averageRating, 1) }}</span>
                    <div class="pb-1.5">
                        <x-shop.star-rating :rating="$this->averageRating" />
                        <p class="mt-1 text-xs text-ink-soft">{{ __('messages.reviews.based_on_reviews', ['count' => $this->totalReviews]) }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach ($this->ratingBreakdown as $stars => $breakdown)
                        <div class="flex items-center gap-3 text-xs text-ink-soft">
                            <span class="w-14 shrink-0">{{ $stars }} {{ __('messages.reviews.stars') }}</span>
                            <div class="h-1 flex-1 overflow-hidden rounded-full bg-line">
                                <div class="h-full rounded-full bg-accent transition-all duration-500" style="width: {{ $breakdown['percentage'] }}%"></div>
                            </div>
                            <span class="w-6 shrink-0 text-end tabular-nums">{{ $breakdown['count'] }}</span>
                        </div>
                    @endforeach
                </div>

                @if (auth()->check() && $this->userHasReviewed)
                    <p class="flex items-center gap-2 rounded-xl bg-accent-soft px-4 py-3 text-sm text-accent-dark">
                        <x-heroicon-o-check-badge class="size-5 shrink-0" />
                        {{ __('messages.reviews.already_reviewed') }}
                    </p>
                @endif
            </aside>
        @endif

        <div @class(['space-y-10', 'lg:col-span-8' => $this->totalReviews > 0, 'lg:col-span-12' => $this->totalReviews === 0])>

            {{-- Form --}}
            @if ($showReviewForm && auth()->check() && ! $this->userHasReviewed)
                <x-shop.panel :title="__('messages.reviews.write_your_review')">
                    <form wire:submit="submitReview" class="space-y-5">
                        <div>
                            <x-shop.label>{{ __('messages.reviews.rating') }}</x-shop.label>
                            <div class="flex items-center gap-1">
                                @for ($star = 1; $star <= 5; $star++)
                                    <button type="button" wire:click="$set('newRating', {{ $star }})" aria-label="{{ $star }}"
                                        class="transition-transform hover:scale-110">
                                        <x-heroicon-s-star @class(['size-7', $star <= $newRating ? 'text-accent' : 'text-line'])/>
                                    </button>
                                @endfor
                                <span class="ms-2 text-xs text-ink-soft">
                                    {{ $newRating }} {{ $newRating === 1 ? __('messages.reviews.star') : __('messages.reviews.stars') }}
                                </span>
                            </div>
                            <x-shop.field-error for="newRating" />
                        </div>

                        <div>
                            <x-shop.label for="comment">{{ __('messages.reviews.comment') }}</x-shop.label>
                            <x-textarea wire:model="newComment" id="comment" rows="4" :placeholder="__('messages.reviews.comment_placeholder')" />
                            <x-shop.field-error for="newComment" />
                        </div>

                        <div class="flex justify-end gap-3">
                            <x-shop.button variant="ghost" size="sm" wire:click="toggleReviewForm">{{ __('messages.reviews.cancel') }}</x-shop.button>
                            <x-shop.button type="submit" size="sm" wire:loading.attr="disabled" wire:target="submitReview">
                                <span wire:loading.remove wire:target="submitReview">{{ __('messages.reviews.submit') }}</span>
                                <span wire:loading wire:target="submitReview">{{ __('messages.reviews.submitting') }}</span>
                            </x-shop.button>
                        </div>
                    </form>
                </x-shop.panel>
            @endif

            {{-- List --}}
            @if ($reviews->isNotEmpty())
                <div class="divide-y divide-line">
                    @foreach ($reviews as $review)
                        <article wire:key="review-{{ $review->id }}" class="flex gap-4 py-8 first:pt-0">
                            @if ($avatar = $review->user->getFirstMediaUrl('profile_photo'))
                                <img src="{{ $avatar }}" alt="{{ $review->user->name }}" class="size-11 shrink-0 rounded-full object-cover">
                            @else
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-accent-soft font-display text-lg text-accent-dark">
                                    {{ mb_strtoupper(mb_substr($review->user->name, 0, 1)) }}
                                </span>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="text-sm font-medium text-ink">{{ $review->user->name }}</h4>
                                    <time class="text-xs text-muted" datetime="{{ $review->created_at->toIso8601String() }}">
                                        {{ $review->created_at->format('d M Y') }}
                                    </time>
                                </div>
                                <x-shop.star-rating :rating="$review->stars" size="size-3.5" class="mt-1.5" />
                                <p class="mt-3 text-sm leading-relaxed text-ink-soft">{{ $review->comment }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($reviews->hasPages())
                    <div>{{ $reviews->links() }}</div>
                @endif
            @else
                <x-shop.empty-state icon="heroicon-o-chat-bubble-left-right" class="py-10!"
                    :title="__('messages.reviews.no_reviews')"
                    :description="__('messages.reviews.be_first_to_review')" />
            @endif
        </div>
    </div>
</div>
