<?php

namespace App\Observers;

use App\Models\Order;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // send notification to admin
        // Notification::make()
        //     ->title('New order arrived')
        //     ->actions([
        //         Action::make('View Order')->button()->url(route('filament.seller.resources.orders.index'))
        //     ])
        //     ->sendToDatabase($order->seller);

    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Notification::make()
        //     ->title('Order has been '.$order->status->value)
        //     ->sendToDatabase($order->seller);

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
