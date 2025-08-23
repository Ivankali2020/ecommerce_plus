<?php

namespace App\Observers;

use App\Helper\OrderStatus;
use App\Jobs\SendOrderTelegramNotification;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Services\PointService;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function __construct(
        protected PointService $pointService
    ) {}

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Send notification to admin in Filament
        Notification::make()
            ->title('New order arrived')
            ->actions([
                Action::make('View Order')->button()->url(route('filament.seller.resources.orders.index')),
            ])
            ->sendToDatabase($order->seller);

        // Send Telegram notification to seller with a delay to ensure order products are saved
        SendOrderTelegramNotification::dispatch($order, 'created')->delay(now()->addSeconds(5));

        // Clear recent orders and user orders cache for user
        $this->clearRecentOrdersCache($order->user_id);
        $this->clearUserOrdersCache($order->user_id);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if order status changed to 'delivered' and user exists
        if (
            $order->isDirty('status') &&
            $order->status === OrderStatus::Delivered &&
            $order->user &&
            ! $order->walk_in_customer
        ) {

            // Check if points haven't been awarded yet for this order
            if (! $this->hasPointsBeenAwarded($order)) {
                try {
                    // Award loyalty points for delivered order
                    $this->pointService->awardOrderPoints($order->user, $order);

                    // Optional: Send notification to user about points earned
                    $pointsEarned = $this->pointService->calculatePointsFromOrder($order->sub_total, $order->user);

                    if ($pointsEarned > 0) {
                        Notification::make()
                            ->title('Points Earned!')
                            ->body("You earned {$pointsEarned} loyalty points from your order #{$order->invoice_number}")
                            ->success()
                            ->sendToDatabase($order->user);
                    }
                } catch (Exception $e) {
                    // Log error if points awarding fails
                    Log::error('Failed to award points for order: '.$order->id, [
                        'error' => $e->getMessage(),
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                    ]);
                }
            }
        }

        // Send Telegram notification for status updates
        if ($order->isDirty('status')) {
            SendOrderTelegramNotification::dispatch($order, 'updated');
        }

        // Clear recent orders and user orders cache for user
        $this->clearRecentOrdersCache($order->user_id);
        $this->clearUserOrdersCache($order->user_id);
    }
    /**
     * Clear the user orders cache for a user (all pages)
     */
    protected function clearUserOrdersCache($userId): void
    {
        // Remove all cached pages for this user's orders
        foreach (range(1, 10) as $page) {
            Cache::forget('user_orders_' . $userId . '_page_' . $page);
        }
    }
    /**
     * Clear the recent orders cache for a user (all pages)
     */
    protected function clearRecentOrdersCache($userId): void
    {
        // Remove all cached pages for this user's recent orders
        foreach (range(1, 10) as $page) {
            Cache::forget('recent_orders_user_' . $userId . '_page_' . $page);
        }
    }

    /**
     * Check if loyalty points have already been awarded for this order
     */
    protected function hasPointsBeenAwarded(Order $order): bool
    {
        return PointTransaction::where('user_id', $order->user_id)
            ->where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'earned')
            ->exists();
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
