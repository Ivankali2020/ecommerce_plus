<?php

namespace App\Services;

use App\Models\User;
use App\Models\Gift;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class PointService
{
    /**
     * Add points to a user's account
     */
    public function addPoints(
        User $user,
        int $points,
         $referenceType = null,
         $referenceId = null,
         $description = null,
        array $metadata = []
    ): PointTransaction {
        return DB::transaction(function () use ($user, $points, $referenceType, $referenceId, $description, $metadata) {
            // Update user's point balance
            $user->increment('points', $points);
            $user->refresh();

            // Create transaction record
            return PointTransaction::create([
                'user_id' => $user->id,
                'seller_id' => $user->seller_id,
                'tenant_id' => $user->tenant_id,
                'type' => 'earned',
                'points' => $points,
                'balance_after' => $user->points,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description ?? "Earned {$points} points",
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Deduct points from a user's account
     */
    public function deductPoints(
        User $user,
        int $points,
        $referenceType = null,
        $referenceId = null,
        $description = null,
        array $metadata = []
    ): PointTransaction {
        if ($user->points < $points) {
            throw new Exception('Insufficient points balance');
        }

        return DB::transaction(function () use ($user, $points, $referenceType, $referenceId, $description, $metadata) {
            // Update user's point balance
            $user->decrement('points', $points);
            $user->refresh();

            // Create transaction record
            return PointTransaction::create([
                'user_id' => $user->id,
                'seller_id' => $user->seller_id,
                'tenant_id' => $user->tenant_id,
                'type' => 'spent',
                'points' => -$points, // Negative for spent points
                'balance_after' => $user->points,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description ?? "Spent {$points} points",
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Calculate points earned from order subtotal
     */
    public function calculatePointsFromOrder(float $subtotal, User $user): int
    {
        // Get point percentage from seller's settings
        $pointPercentage = $user->seller->setting->data['point_percentage'] ?? 0;

        // Calculate points (subtotal * percentage / 100)
        $points = (int) floor($subtotal * $pointPercentage / 100);

        return max(0, $points); // Ensure non-negative
    }

    /**
     * Award points for completed order
     */
    public function awardOrderPoints(User $user, $order): PointTransaction
    {
        $points = $this->calculatePointsFromOrder($order->sub_total, $user);

        return $this->addPoints(
            user: $user,
            points: $points,
            referenceType: 'order',
            referenceId: $order->id,
            description: "Earned {$points} points from order #{$order->invoice_number}",
            metadata: [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'subtotal' => $order->sub_total,
                'point_percentage' => $user->seller->setting->data['point_percentage'] ?? 0,
            ]
        );
    }

    /**
     * Exchange points for a gift
     */
    public function exchangeForGift(User $user, Gift $gift, string $deliveryAddress = null): array
    {
        if (!$gift->is_active) {
            throw new Exception('Gift is not available');
        }

        if ($gift->stock_quantity <= 0) {
            throw new Exception('Gift is out of stock');
        }

        if ($user->points < $gift->points_required) {
            throw new Exception('Insufficient points for this gift');
        }

        return DB::transaction(function () use ($user, $gift, $deliveryAddress) {
            // Deduct stock
            $gift->decrement('stock_quantity');

            // Deduct points and create transaction
            $pointTransaction = $this->deductPoints(
                user: $user,
                points: $gift->points_required,
                referenceType: 'gift',
                referenceId: $gift->id,
                description: "Exchanged {$gift->points_required} points for {$gift->name}",
                metadata: [
                    'gift_id' => $gift->id,
                    'gift_name' => $gift->name,
                    'gift_description' => $gift->description,
                ]
            );

            // Create gift exchange record
            $giftExchange = \App\Models\GiftExchange::create([
                'user_id' => $user->id,
                'gift_id' => $gift->id,
                'seller_id' => $gift->seller_id,
                'tenant_id' => $gift->tenant_id,
                'point_transaction_id' => $pointTransaction->id,
                'points_spent' => $gift->points_required,
                'status' => 'pending',
                'delivery_address' => $deliveryAddress,
                'gift_snapshot' => [
                    'name' => $gift->name,
                    'description' => $gift->description,
                    'points_required' => $gift->points_required,
                    'image' => $gift->image,
                    'additional_data' => $gift->additional_data,
                ],
            ]);

            return [
                'transaction' => $pointTransaction,
                'exchange' => $giftExchange,
            ];
        });
    }

    /**
     * Award referral points
     */
    public function awardReferralPoints(User $user, User $referredUser): PointTransaction
    {
        // Get referral points from seller's settings
        $referralPoints = $user->seller->setting->data['referral_points'] ?? 0;

        return $this->addPoints(
            user: $user,
            points: $referralPoints,
            referenceType: 'referral',
            referenceId: $referredUser->id,
            description: "Earned {$referralPoints} points for referring {$referredUser->name}",
            metadata: [
                'referred_user_id' => $referredUser->id,
                'referred_user_name' => $referredUser->name,
            ]
        );
    }

    /**
     * Get user's point transaction history
     */
    public function getTransactionHistory(User $user, int $limit = 50)
    {
        return PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's points summary
     */
    public function getPointsSummary(User $user): array
    {
        $transactions = PointTransaction::where('user_id', $user->id);

        return [
            'current_balance' => $user->points,
            'total_earned' => $transactions->clone()->where('type', 'earned')->sum('points'),
            'total_spent' => abs($transactions->clone()->where('type', 'spent')->sum('points')),
            'total_expired' => abs($transactions->clone()->where('type', 'expired')->sum('points')),
            'transaction_count' => $transactions->count(),
        ];
    }
}
