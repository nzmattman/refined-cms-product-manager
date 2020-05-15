<?php

namespace RefinedDigital\ProductManager\Module\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderStatusUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $order;
    protected $status;

    /**
     * Create a new event instance.
     *
     */
    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return [];
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getStatus()
    {
        return $this->status;
    }

}
