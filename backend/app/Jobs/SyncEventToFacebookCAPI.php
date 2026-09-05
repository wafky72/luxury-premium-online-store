<?php

namespace App\Jobs;

use App\Models\NotificationsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Syncs a purchase/add-to-cart/etc. event to Facebook Conversions API.
 * Runs on the 'tracking' queue.
 */
class SyncEventToFacebookCAPI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(protected array $payload) {}

    public function handle(): void
    {
        $pixelId     = config('services.facebook.pixel_id');
        $accessToken = config('services.facebook.access_token');

        if (! $pixelId || ! $accessToken) {
            Log::warning('Facebook CAPI not configured — skipping.');
            return;
        }

        $data = [
            'data' => [[
                'event_name'        => $this->payload['event_name'],
                'event_time'        => $this->payload['event_time'],
                'event_id'          => $this->payload['event_id'] ?? uniqid(),
                'action_source'     => 'website',
                'user_data'         => array_filter([
                    'ph' => $this->payload['user_phone'] ?? null,
                    'em' => $this->payload['user_email'] ?? null,
                ]),
                'custom_data'       => array_filter([
                    'value'         => $this->payload['value'] ?? null,
                    'currency'      => $this->payload['currency'] ?? 'BDT',
                    'order_id'      => $this->payload['order_number'] ?? null,
                    'content_ids'   => $this->payload['product_ids'] ?? [],
                    'content_type'  => 'product',
                ]),
            ]],
        ];

        $response = Http::timeout(10)
            ->post("https://graph.facebook.com/v18.0/{$pixelId}/events?access_token={$accessToken}", $data);

        if (! $response->successful()) {
            Log::error('Facebook CAPI sync failed', [
                'payload'  => $this->payload,
                'response' => $response->body(),
            ]);
            $this->fail('CAPI sync failed');
        }
    }
}
