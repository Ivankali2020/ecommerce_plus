<?php

namespace App\Observers;

use App\Models\Seller;
use App\Models\SellerSubscription;
use Carbon\Carbon;

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
        
        $seller->assignRole(['panel_user', 'super_admin']);

    }
}