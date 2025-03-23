<?php

namespace App\Providers;

use App\Models\Exchange;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Observers\ExchangeObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $observers = [
        // Order::class => [OrderObserver::class],
        // OrderProduct::class => [OrderProductObserver::class],
        Exchange::class => [ExchangeObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
