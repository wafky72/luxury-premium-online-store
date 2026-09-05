<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\NotificationService;
use App\Jobs\SyncEventToFacebookCAPI;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOrderPlaced implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(protected NotificationService $notifications) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // 1. Notifications (these dispatch their own queued jobs)
        $this->notifications->orderPlaced($order);

        // 2. Facebook Conversions API — server-side purchase event
        dispatch(new SyncEventToFacebookCAPI([
            'event_name'    => 'Purchase',
            'event_time'    => now()->timestamp,
            'event_id'      => 'order_' . $order->id,
            'user_phone'    => $order->recipient_phone,
            'value'         => $order->total,
            'currency'      => 'BDT',
            'order_id'      => $order->id,
            'order_number'  => $order->order_number,
        ]))->onQueue('tracking');
    }
}
