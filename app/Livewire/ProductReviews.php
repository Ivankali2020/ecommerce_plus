<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Review;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProductReviews extends Component
{
    use WithPagination;

    public Product $product;

    // Form properties for new review
    public int $newRating = 5;

    public string $newComment = '';

    // UI state
    public bool $showReviewForm = false;

    protected array $rules = [
        'newRating' => ['required', 'integer', 'between:1,5'],
        'newComment' => ['required', 'string', 'min:10', 'max:1000'],
    ];

    protected array $messages = [
        'newRating.required' => 'Rating is required.',
        'newRating.between' => 'Rating must be between 1 and 5 stars.',
        'newComment.required' => 'Comment is required.',
        'newComment.min' => 'Comment must be at least 10 characters.',
        'newComment.max' => 'Comment cannot exceed 1000 characters.',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function toggleReviewForm(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->showReviewForm = ! $this->showReviewForm;

        if (! $this->showReviewForm) {
            $this->resetForm();
        }
    }

    public function submitReview(): void
    {
        if (! Auth::check()) {
            return;
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($existingReview) {
            Notification::make()
                ->title('Review Error')
                ->body('You have already submitted a review for this product.')
                ->danger()
                ->send();

            return;
        }

        $this->validate();

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'stars' => $this->newRating,
            'comment' => $this->newComment,
        ]);

        $this->resetForm();
        $this->showReviewForm = false;

        // Clear caches after adding review
        $this->clearReviewCaches();

        Notification::make()
            ->title('Review Submitted')
            ->body('Thank you for your feedback!')
            ->success()
            ->send();

        // Reset pagination to show the latest review
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->newRating = 5;
        $this->newComment = '';
        $this->resetValidation();
    }

    protected function clearReviewCaches(): void
    {
        Cache::forget("avg_rating_product_{$this->product->id}");
        Cache::forget("total_reviews_product_{$this->product->id}");
        Cache::forget("rating_breakdown_product_{$this->product->id}");
        Cache::forget("user_reviewed_product_{$this->product->id}_".(Auth::id() ?? 'guest'));
    }

    #[Computed]
    public function averageRating(): float
    {
        return Cache::remember("avg_rating_product_{$this->product->id}", 1800, function () {
            return $this->product->reviews()->avg('stars') ?: 0;
        });
    }

    #[Computed]
    public function totalReviews(): int
    {
        return Cache::remember("total_reviews_product_{$this->product->id}", 1800, function () {
            return $this->product->reviews()->count();
        });
    }

    #[Computed]
    public function ratingBreakdown(): array
    {
        return Cache::remember("rating_breakdown_product_{$this->product->id}", 1800, function () {
            $breakdown = [];
            $totalReviews = $this->totalReviews;

            for ($i = 5; $i >= 1; $i--) {
                $count = $this->product->reviews()->where('stars', $i)->count();
                $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;

                $breakdown[$i] = [
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }

            return $breakdown;
        });
    }

    #[Computed]
    public function userHasReviewed(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Cache::remember("user_reviewed_product_{$this->product->id}_".Auth::id(), 1800, function () {
            return Review::where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->exists();
        });
    }

    public function render()
    {
        $reviews = $this->product->reviews()
            ->select(['id', 'user_id', 'product_id', 'stars', 'comment', 'created_at'])
            ->with([
                'user' => function ($query) {
                    $query->select(['id', 'name'])->with('media');
                },
            ])
            ->latest()
            ->paginate(5);

        return view('livewire.product-reviews', [
            'reviews' => $reviews,
        ]);
    }
}
