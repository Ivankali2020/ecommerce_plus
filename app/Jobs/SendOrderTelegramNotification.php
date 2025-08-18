<?php

namespace App\Jobs;

use App\Models\Order;
use App\Helper\OrderStatus;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class SendOrderTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $action
    ) {}

    public function handle(): void
    {
        // Check if seller has telegram_chat_id
        if (!$this->order->seller || !$this->order->seller->telegram_chat_id) {
            return;
        }

        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            $message = match($this->action) {
                'created' => $this->formatNewOrderMessage($this->order),
                'updated' => $this->formatOrderUpdateMessage($this->order),
                default => "Order #{$this->order->invoice_number} has been {$this->action}"
            };
            
            $telegram->sendMessage([
                'chat_id' => $this->order->seller->telegram_chat_id,
                'text' => $message,
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to send Telegram notification to seller', [
                'error' => $e->getMessage(),
                'order_id' => $this->order->id,
                'seller_id' => $this->order->seller_id,
                'telegram_chat_id' => $this->order->seller->telegram_chat_id,
            ]);
        }
    }

    /**
     * Format new order message for Telegram
     */
    protected function formatNewOrderMessage(Order $order): string
    {
        $customerName = $order->walk_in_customer ? 'Walk-in Customer' : $order->user->name;
        
        // Fresh query to get the actual item count from database
        $itemCount = $order->products()->sum('quantity') ?: 0;
        
        $message = "🛍️ New Order Received!\n\n" .
                   "📋 Order #: {$order->invoice_number}\n" .
                   "👤 Customer: {$customerName}\n" .
                   "📦 Items: {$itemCount} item(s)\n" .
                   "💰 Total: " . number_format($order->total_price, 0) . " MMK\n" .
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
            OrderStatus::New => '🆕',
            OrderStatus::Processing => '⏳',
            OrderStatus::Shipped => '🚚',
            OrderStatus::Delivered => '✅',
            OrderStatus::Cancelled => '❌',
            OrderStatus::Refunded => '💰',
            default => '📦'
        };
        
        $message = "{$statusEmoji} Order Status Updated\n\n" .
                   "📋 Order #: {$order->invoice_number}\n" .
                   "🔄 Status: {$order->status->value}\n" .
                   "💰 Total: " . number_format($order->total_price, 0) . " MMK\n" .
                   "🕐 Updated: " . now()->format('M d, Y h:i A');
        
        // Add note if it exists
        if (!empty($order->note)) {
            $message .= "\n📝 Note: " . $order->note;
        }
        
        return $message;
    }
}
