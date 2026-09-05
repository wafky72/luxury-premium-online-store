<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Jobs\SendEmailJob;
use App\Models\NotificationsLog;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /** Send an SMS via BulkSMSBD, queued async */
    public function sms(string $phone, string $message, array $meta = []): void
    {
        $log = NotificationsLog::create([
            'channel'   => 'sms',
            'recipient' => $phone,
            'template'  => $meta['template'] ?? 'generic',
            'content'   => $message,
            'status'    => 'pending',
            'order_id'  => $meta['order_id'] ?? null,
            'user_id'   => $meta['user_id'] ?? null,
        ]);

        dispatch(new SendSmsJob($phone, $message, $log->id))
            ->onQueue('notifications');
    }

    /** Send an email, queued async */
    public function email(string $to, string $template, array $data = [], array $meta = []): void
    {
        $log = NotificationsLog::create([
            'channel'   => 'email',
            'recipient' => $to,
            'template'  => $template,
            'content'   => json_encode($data),
            'status'    => 'pending',
            'order_id'  => $meta['order_id'] ?? null,
            'user_id'   => $meta['user_id'] ?? null,
        ]);

        dispatch(new SendEmailJob($to, $template, json_encode($data), $log->id))
            ->onQueue('notifications');
    }

    /** Convenience: send order placed notifications */
    public function orderPlaced(\App\Models\Order $order): void
    {
        $message = "✅ আপনার অর্ডার নিশ্চিত হয়েছে! অর্ডার নং: {$order->order_number}। ধন্যবাদ Tory Crown কে বেছে নেওয়ার জন্য।";
        $this->sms($order->recipient_phone, $message, ['template' => 'order_placed', 'order_id' => $order->id]);

        if ($order->user?->email) {
            $this->email($order->user->email, 'order-placed', [
                'order'    => $order->load('items'),
                'customer' => $order->recipient_name,
            ], ['order_id' => $order->id]);
        }
    }

    /** Convenience: send shipping update */
    public function orderShipped(\App\Models\Order $order): void
    {
        $tracking = $order->shipment?->tracking_number ?? 'N/A';
        $message  = "🚚 আপনার অর্ডার ({$order->order_number}) পাঠানো হয়েছে! ট্র্যাকিং: {$tracking}";
        $this->sms($order->recipient_phone, $message, ['template' => 'order_shipped', 'order_id' => $order->id]);
    }
}
