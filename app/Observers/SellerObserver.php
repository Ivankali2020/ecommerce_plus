<?php

namespace App\Observers;

use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SellerObserver
{
    /**
     * Handle the Seller "created" event.
     */
    public function created(Seller $seller): void
    {
        // Skip if this is a staff account (has parent_id)
        if ($seller->parent_id) {
            return;
        }

        // Set default free subscription (ID: 1) for one month
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonth();

        // Update seller with subscription details
        $seller->update([
            'subscription_plan_id' => 1, // Free plan ID
            'subscription_started_at' => $startDate,
            'subscription_expired_at' => $endDate,
            'subscription_status' => 'active',
        ]);

        // ADD default settings for the seller (only if it doesn't exist)
        $seller->setting()->firstOrCreate(
            [
                'seller_id' => $seller->id,
                'tenant_id' => $seller->tenant_id,
            ],
            [
                'data' => [
                    'point_percentage' => 5, // 5% point reward
                    'tax_percentage' => 10, // 10% tax
                    'referral_points' => 100, // 100 points for referral
                    // Social links (empty by default)
                    'facebook' => '',
                    'twitter' => '',
                    'youtube' => '',
                    'instagram' => '',
                    // Content pages (empty by default)
                    'privacy_policy' => '',
                    'terms_conditions' => '',
                    'about_us' => '',
                    'returns_exchanges' => '',
                    // FAQ (empty array by default)
                    'faq' => [],
                ],
            ]
        );

        // Attach tenant to seller
        DB::table('tenant_seller')->insert([
            'tenant_id' => $seller->tenant_id,
            'seller_id' => $seller->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seller->assignRole(['super_admin']); // 
    }

    /**
     * Handle the Seller "updated" event.
     */
    public function updated(Seller $seller): void
    {
        $this->clearSellerCache($seller);
    }

    /**
     * Handle the Seller "deleted" event.
     */
    public function deleted(Seller $seller): void
    {
        $this->clearSellerCache($seller);
    }

    /**
     * Clear seller-related cache.
     */
    private function clearSellerCache(Seller $seller): void
    {
        $pointsPercentageKey = "seller_points_percentage_{$seller->id}";
        Cache::forget($pointsPercentageKey);
    }
}
