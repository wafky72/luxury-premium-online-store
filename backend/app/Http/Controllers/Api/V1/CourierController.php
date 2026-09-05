<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Courier\SteadfastService;

class CourierController extends Controller
{
    /**
     * Handle Steadfast Webhook
     */
    public function webhookSteadfast(Request $request, SteadfastService $steadfast)
    {
        // Signature verification
        $signature = $request->header('X-Steadfast-Signature');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), config('services.steadfast.api_secret', ''));

        if (!hash_equals($expectedSignature, $signature ?? '')) {
            Log::warning('Steadfast Webhook invalid signature', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $steadfast->handleWebhook($request->all());

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Pathao Webhook
     */
    public function webhookPathao(Request $request)
    {
        Log::info('Pathao Webhook', $request->all());
        // Pathao implementation pending
        return response()->json(['status' => 'success']);
    }
}
