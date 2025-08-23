<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Review extends Model
{
    use BelongsToPrimaryModel, HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'stars',
        'comment',
    ];

    protected $casts = [
        'stars' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($review) {
            static::clearReviewCaches($review->product_id, $review->user_id);
        });

        static::deleted(function ($review) {
            static::clearReviewCaches($review->product_id, $review->user_id);
        });
    }

    protected static function clearReviewCaches($productId, $userId): void
    {
        Cache::forget("avg_rating_product_{$productId}");
        Cache::forget("total_reviews_product_{$productId}");
        Cache::forget("rating_breakdown_product_{$productId}");
        Cache::forget("user_reviewed_product_{$productId}_{$userId}");
    }

    public function getRelationshipToPrimaryModel(): string
    {
        return 'product';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
