<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Coupon;
use App\Events\OrderPlaced;
use App\Services\FraudDetectionService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected FraudDetectionService $fraudService
    ) {}

    /**
     * Place an order from the current cart.
     * Wrapped in a DB transaction for atomicity.
     */
    public function place(array $data, Cart $cart): Order
    {
        return DB::transaction(function () use ($data, $cart) {
            $items    = $cart->load('items.product', 'items.variant')->items;
            $subtotal = $items->sum(fn($i) => $i->price_snapshot * $i->qty);
            $coupon   = $cart->coupon_code ? Coupon::where('code', $cart->coupon_code)->first() : null;
            $discount = $cart->coupon_discount ?? 0;
            $shipping = $this->calculateShipping($data['district']);
            $vat      = round($subtotal * 0.05, 2);
            $total    = max(0, $subtotal - $discount + $shipping + $vat);

            // Step 1: Reserve stock atomically
            foreach ($items as $item) {
                if ($item->variant && ! $item->variant->reserveStock($item->qty)) {
                    throw new \RuntimeException("STOCK_UNAVAILABLE:{$item->product->name}");
                }
            }

            // Step 2: Run fraud detection
            $fraud = $this->fraudService->check($data['phone'], $data['district']);

            // Step 3: Create order
            $order = Order::create([
                'user_id'          => auth()->id(),
                'coupon_id'        => $coupon?->id,
                'recipient_name'   => $data['name'],
                'recipient_phone'  => $data['phone'],
                'shipping_address' => $data['address'],
                'shipping_city'    => $data['city'],
                'shipping_district'=> $data['district'],
                'subtotal'         => $subtotal,
                'shipping_fee'     => $shipping,
                'coupon_discount'  => $discount,
                'vat'              => $vat,
                'total'            => $total,
                'payment_method'   => $data['payment_method'],
                'courier'          => $data['courier'] ?? $this->selectCourier($data['district']),
                'fraud_score'      => $fraud['score'],
                'fraud_data'       => $fraud,
                'source'           => 'web',
            ]);

            // Step 4: Create order items (denormalized snapshots)
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'variant_id'   => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'sku'          => $item->variant?->sku ?? $item->product->sku,
                    'karat'        => $item->variant?->karat,
                    'weight_grams' => $item->variant?->weight_grams,
                    'image_url'    => $item->product->primaryImage->first()?->url,
                    'qty'          => $item->qty,
                    'unit_price'   => $item->price_snapshot,
                    'total_price'  => $item->price_snapshot * $item->qty,
                ]);
            }

            // Step 5: Increment coupon usage
            $coupon?->increment('used_count');

            // Step 6: Clear cart
            $this->cartService->clear();

            // Step 7: Dispatch event (sends SMS, email, FB CAPI async)
            event(new OrderPlaced($order));

            // Flag high-risk orders instead of auto-confirming
            if ($fraud['score'] >= 75) {
                $order->update(['status' => 'flagged']);
            }

            return $order->fresh(['items', 'shipment']);
        });
    }

    public function cancel(Order $order, string $reason = ''): bool
    {
        if (! $order->isCancellable()) {
            throw new \RuntimeException('ORDER_NOT_CANCELLABLE');
        }

        DB::transaction(function () use ($order, $reason) {
            // Release reserved stock
            foreach ($order->items as $item) {
                $item->variant?->releaseStock($item->qty);
            }
            $order->update(['status' => 'cancelled', 'notes' => $reason]);
        });

        return true;
    }

    protected function calculateShipping(string $district): float
    {
        $dhakaDistricts = ['Dhaka', 'Narayanganj', 'Gazipur'];
        return in_array($district, $dhakaDistricts) ? 60.00 : 120.00;
    }

    protected function selectCourier(string $district): string
    {
        $dhakaDistricts = ['Dhaka', 'Narayanganj', 'Gazipur'];
        return in_array($district, $dhakaDistricts) ? 'steadfast' : 'pathao';
    }
}
