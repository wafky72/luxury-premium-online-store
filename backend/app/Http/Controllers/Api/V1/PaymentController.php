<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\Payment\BkashGateway;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|in:bkash,nagad,sslcommerz,cod'
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => false, 'error' => 'Order already paid'], 400);
        }

        if ($validated['gateway'] === 'cod') {
            $order->update(['payment_method' => 'cod', 'payment_status' => 'pending']);
            return response()->json(['success' => true, 'message' => 'COD selected']);
        }

        if ($validated['gateway'] === 'bkash') {
            // Initiate bKash payment logic
            $bkash = app(BkashGateway::class);
            $response = $bkash->initiate($order);
            
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Gateway not fully implemented'], 501);
    }

    public function verify(Request $request, $transactionId)
    {
        // General verification endpoint for frontend to poll status
        return response()->json(['success' => true, 'status' => 'pending']);
    }

    public function webhookBkash(Request $request)
    {
        // Handle incoming bKash webhooks (payment success, failed, etc)
        Log::info('bKash Webhook received', $request->all());
        // Verify signature and update order status
        return response()->json(['status' => 'acknowledged']);
    }

    public function webhookNagad(Request $request)
    {
        Log::info('Nagad Webhook received', $request->all());
        return response()->json(['status' => 'acknowledged']);
    }

    public function webhookSslcommerz(Request $request)
    {
        Log::info('SSLCommerz Webhook received', $request->all());
        return response()->json(['status' => 'acknowledged']);
    }
}
