<?php

namespace App\Observers;

use App\Helper\OrderStatus;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Services\PointService;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

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

        // Send Telegram notification to seller if they have telegram_chat_id
        $this->sendTelegramOrderNotification($order, 'created');
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

        // Notification::make()
        //     ->title('Order has been '.$order->status->value)
        //     ->sendToDatabase($order->seller);
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
     * Send Telegram notification to seller
     */
    protected function sendTelegramOrderNotification(Order $order, string $action): void
    {
        // Check if seller has telegram_chat_id
        if (!$order->seller || !$order->seller->telegram_chat_id) {
            return;
        }

        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            $message = match($action) {
                'created' => $this->formatNewOrderMessage($order),
                'updated' => $this->formatOrderUpdateMessage($order),
                default => "Order #{$order->invoice_number} has been {$action}"
            };
            
            $telegram->sendMessage([
                'chat_id' => $order->seller->telegram_chat_id,
                'text' => $message,
                // No parse_mode = plain text
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to send Telegram notification to seller', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'telegram_chat_id' => $order->seller->telegram_chat_id,
            ]);
        }
    }

    /**
     * Format new order message for Telegram
     */
    protected function formatNewOrderMessage(Order $order): string
    {
        $customerName = $order->walk_in_customer ? 'Walk-in Customer' : $order->user->name;
        $itemCount = $order->products->sum('quantity');
        
        $message = "🛍️ New Order Received!\n\n" .
                   "📋 Order #: {$order->invoice_number}\n" .
                   "👤 Customer: {$customerName}\n" .
                   "📦 Items: {$itemCount} item(s)\n" .
                   "💰 Total: " . number_format($order->total, 0) . " MMK\n" .
                   "🕐 Time: " . $order->created_at->format('M d, Y h:i A');
        
        // Add note if it exists
        if (!empty($order->note)) {
            $message .= "\n📝 Note: " . $order->note;
        }
        
        $message .= "\n\nCheck your admin panel for full details.";
        
        return $message;
    }

    /**
     * Format order update message for Telegram
     */
    protected function formatOrderUpdateMessage(Order $order): string
    {
        $statusEmoji = match($order->status) {
            OrderStatus::Pending => '⏳',
            OrderStatus::Confirmed => '✅',
            OrderStatus::Delivered => '🚚',
            OrderStatus::Cancelled => '❌',
            default => '📦'
        };
        
        $message = "{$statusEmoji} Order Status Updated\n\n" .
                   "📋 Order #: {$order->invoice_number}\n" .
                   "🔄 Status: {$order->status->value}\n" .
                   "💰 Total: " . number_format($order->total, 0) . " MMK\n" .
                   "🕐 Updated: " . now()->format('M d, Y h:i A');
        
        // Add note if it exists
        if (!empty($order->note)) {
            $message .= "\n📝 Note: " . $order->note;
        }
        
        return $message;
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
