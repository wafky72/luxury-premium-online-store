<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Steadfast Courier Integration
 * Docs: https://steadfast.com.bd/user/api
 */
class SteadfastService implements CourierInterface
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl = 'https://portal.steadfast.com.bd/api/v1';

    public function __construct()
    {
        $this->apiKey    = config('services.steadfast.api_key');
        $this->apiSecret = config('services.steadfast.api_secret');
    }

    public function createConsignment(Order $order): array
    {
        $response = Http::withHeaders([
            'Api-Key'    => $this->apiKey,
            'Secret-Key' => $this->apiSecret,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/create_order", [
            'invoice'          => $order->order_number,
            'recipient_name'   => $order->recipient_name,
            'recipient_phone'  => $order->recipient_phone,
            'recipient_address'=> $order->shipping_address . ', ' . $order->shipping_city,
            'cod_amount'       => $order->payment_method === 'cod' ? $order->total : 0,
            'note'             => $order->notes ?? '',
        ]);

        if ($response->successful() && $response['status'] === 200) {
            $data = $response['data'];
            $shipment = Shipment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'courier_slug'     => 'steadfast',
                    'consignment_id'   => $data['consignment_id'],
                    'tracking_number'  => $data['tracking_code'],
                    'status'           => 'pending',
                    'charge'           => $data['charge'] ?? 60,
                ]
            );

            $order->update(['courier' => 'steadfast', 'status' => 'processing']);

            return ['success' => true, 'shipment' => $shipment];
        }

        Log::error('Steadfast consignment creation failed', ['response' => $response->body()]);
        return ['success' => false, 'error' => $response->json('message', 'Steadfast API error')];
    }

    public function getStatus(string $consignmentId): array
    {
        $response = Http::withHeaders([
            'Api-Key'    => $this->apiKey,
            'Secret-Key' => $this->apiSecret,
        ])->get("{$this->baseUrl}/status_by_cid/{$consignmentId}");

        return $response->successful()
            ? ['success' => true, 'status' => $response['delivery_status']]
            : ['success' => false];
    }

    /** Handle inbound Steadfast webhook and update shipment status */
    public function handleWebhook(array $payload): void
    {
        $consignmentId = $payload['consignment_id'] ?? null;
        if (! $consignmentId) return;

        $shipment = Shipment::where('consignment_id', $consignmentId)->first();
        if (! $shipment) return;

        $statusMap = [
            'in_review'       => 'pending',
            'confirmed'       => 'picked_up',
            'in_transit'      => 'in_transit',
            'delivered'       => 'delivered',
            'partial_delivered'=> 'delivered',
            'cancelled'       => 'failed',
            'returned'        => 'returned',
        ];

        $newStatus = $statusMap[$payload['delivery_status']] ?? $shipment->status;
        $shipment->update([
            'status'       => $newStatus,
            'webhook_data' => $payload,
            'delivered_at' => $newStatus === 'delivered' ? now() : $shipment->delivered_at,
        ]);

        // Sync order status
        if ($newStatus === 'delivered') {
            $shipment->order?->update(['status' => 'delivered']);
        }

        // Trigger SMS notification
        app(\App\Services\NotificationService::class)->orderShipped($shipment->order);
    }
}
