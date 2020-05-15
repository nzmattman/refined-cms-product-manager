<?php

namespace RefinedDigital\ProductManager\Module\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderRepository;

class SendOrderNotification
{

    protected $orderRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->orderRepository->sendNotification($event->getOrder(), $event->getStatus());
    }
}
