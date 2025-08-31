<?php

namespace App\Providers;

use App\Listeners\AwardReferralPoints;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\OrderReturnProduct;
use App\Observers\ExchangeObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderReturnProductObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
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
        Verified::class => [
            AwardReferralPoints::class,
        ],
    ];

    protected $observers = [
        Exchange::class => [ExchangeObserver::class],
        Order::class => [OrderObserver::class],
        OrderReturnProduct::class => [OrderReturnProductObserver::class],
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
