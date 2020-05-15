<?php

namespace RefinedDigital\ProductManager\Module\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use RefinedDigital\ProductManager\Module\Events\OrderStatusUpdatedEvent;
use RefinedDigital\ProductManager\Module\Listeners\SendOrderNotification;

class ProductManagerEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderStatusUpdatedEvent::class => [
            SendOrderNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
