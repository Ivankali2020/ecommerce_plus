<?php

namespace App\Livewire\Seller;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionInfoCard extends Component
{
    public function render()
    {
        $seller = Auth::guard('seller')->user();

        $subscriptionData = [
            'hasSubscription' => false,
            'planName' => null,
            'expiryDate' => null,
            'daysRemaining' => null,
            'isExpired' => true,
            'status' => 'inactive',
        ];

        if ($seller) {
            // For staff, check parent's subscription
            if ($seller->parent_id) {
                $seller = $seller->parent;
            }

            if ($seller && $seller->subscription_expired_at) {
                $subscriptionData['hasSubscription'] = true;
                $subscriptionData['planName'] = $seller->subscriptionPlan?->name ?? 'Unknown Plan';
                $subscriptionData['expiryDate'] = $seller->subscription_expired_at;
                $subscriptionData['daysRemaining'] = max(0, Carbon::now()->diffInDays($seller->subscription_expired_at, false));
                $subscriptionData['isExpired'] = $seller->subscription_expired_at->isPast();
                $subscriptionData['status'] = $seller->subscription_status;
            }
        }

        return view('livewire.seller.subscription-info-card', [
            'subscriptionData' => $subscriptionData,
        ]);
    }
}
