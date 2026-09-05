<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CartService  $cartService
    ) {}

    /** GET /api/v1/orders — authenticated customer's orders */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items', 'shipment'])
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /** POST /api/v1/orders — place a new order */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'payment_method' => 'required|in:cod,bkash,nagad,sslcommerz',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'VALIDATION_ERROR',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cart = $this->cartService->getCart($request->header('X-Session-ID'));
        
        // Sync cart from request if items are provided
        if ($request->has('items') && is_array($request->items)) {
            $this->cartService->clear($request->header('X-Session-ID'));
            foreach ($request->items as $item) {
                if (isset($item['variant_id']) && isset($item['quantity'])) {
                    $variant = \App\Models\ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $this->cartService->addItem($request->header('X-Session-ID'), $variant, $item['quantity']);
                    }
                }
            }
            $cart = $this->cartService->getCart($request->header('X-Session-ID'));
        }

        if ($cart->items->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'CART_EMPTY'], 400);
        }

        try {
            $order = $this->orderService->place($validator->validated(), $cart);
            return response()->json(['success' => true, 'data' => $order], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'SYSTEM_ERROR', 
                'message' => $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine()
            ], 500);
        }
    }

    /** GET /api/v1/orders/{id} */
    public function show(int $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['items', 'payments', 'shipment', 'timeline'])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $order]);
    }

    /** GET /api/v1/orders/{orderNumber}/track — public tracking */
    public function track(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['shipment', 'timeline'])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => [
            'order_number' => $order->order_number,
            'status'       => $order->status,
            'tracking'     => $order->shipment?->tracking_number,
            'tracking_url' => $order->shipment?->tracking_url,
            'timeline'     => $order->timeline,
        ]]);
    }

    /** POST /api/v1/orders/{id}/cancel */
    public function cancel(int $id, Request $request): JsonResponse
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        try {
            $this->orderService->cancel($order, $request->input('reason', ''));
            return response()->json(['success' => true, 'message' => 'Order cancelled.']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 409);
        }
    }
}
