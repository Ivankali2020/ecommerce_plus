<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Seller;
use Filament\Facades\Filament;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //send notification to admin
        Notification::make()
            ->title('New order arrived')
            ->sendToDatabase($order->seller);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //send to user
        $seller = Seller::where('id',$order->seller_id)->first();

        Notification::make()
            ->title('Order has been '.$order->status->value)
//            ->broadcast($seller);
            ->sendToDatabase($seller);

//        event(new DatabaseNotificationsSent($seller));

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
