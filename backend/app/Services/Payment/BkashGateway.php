<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * bKash Payment Gateway
 * Docs: https://developer.bka.sh/docs
 */
class BkashGateway implements PaymentGatewayInterface
{
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;
    protected string $baseUrl;

    public function __construct()
    {
        $this->appKey    = config('services.bkash.app_key');
        $this->appSecret = config('services.bkash.app_secret');
        $this->username  = config('services.bkash.username');
        $this->password  = config('services.bkash.password');
        $this->baseUrl   = config('services.bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
    }

    public function initiate(Order $order): array
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization'  => $token,
            'X-APP-Key'      => $this->appKey,
            'Content-Type'   => 'application/json',
        ])->post("{$this->baseUrl}/tokenized/checkout/create", [
            'mode'                  => '0011',
            'payerReference'        => $order->recipient_phone,
            'callbackURL'           => route('payment.bkash.callback'),
            'amount'                => (string) $order->total,
            'currency'              => 'BDT',
            'intent'                => 'sale',
            'merchantInvoiceNumber' => $order->order_number,
        ]);

        if ($response->successful() && $response['statusCode'] === '0000') {
            Payment::create([
                'order_id'        => $order->id,
                'gateway'         => 'bkash',
                'gateway_order_id'=> $response['paymentID'],
                'amount'          => $order->total,
                'status'          => 'pending',
                'gateway_response'=> $response->json(),
            ]);

            return ['success' => true, 'redirect_url' => $response['bkashURL']];
        }

        return ['success' => false, 'error' => $response['statusMessage'] ?? 'bKash error'];
    }

    public function verify(string $transactionId): bool
    {
        $token    = $this->getToken();
        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
        ])->post("{$this->baseUrl}/tokenized/checkout/execute", [
            'paymentID' => $transactionId,
        ]);

        if ($response->successful() && $response['statusCode'] === '0000') {
            $payment = Payment::where('gateway_order_id', $transactionId)->first();
            $payment?->update([
                'transaction_id'   => $response['trxID'],
                'status'           => 'completed',
                'gateway_response' => $response->json(),
                'verified_at'      => now(),
            ]);
            $payment?->order?->update(['payment_status' => 'paid']);
            return true;
        }

        return false;
    }

    public function refund(Payment $payment, float $amount): bool
    {
        $token    = $this->getToken();
        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key'     => $this->appKey,
        ])->post("{$this->baseUrl}/tokenized/checkout/payment/refund", [
            'paymentID'  => $payment->gateway_order_id,
            'trxID'      => $payment->transaction_id,
            'amount'     => (string) $amount,
            'currency'   => 'BDT',
            'merchantInvoiceNumber' => $payment->order->order_number,
        ]);

        if ($response->successful() && ($response['statusCode'] ?? '') === '0000') {
            $payment->update(['status' => 'refunded', 'gateway_response' => $response->json()]);
            return true;
        }

        return false;
    }

    protected function getToken(): string
    {
        return \Cache::remember('bkash_token', 3500, function () {
            $response = Http::withHeaders([
                'Content-Type'   => 'application/json',
                'username'       => $this->username,
                'password'       => $this->password,
            ])->post("{$this->baseUrl}/tokenized/checkout/token/grant", [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            return $response['id_token'];
        });
    }
}
