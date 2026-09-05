<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment session.
     * Returns ['success' => bool, 'redirect_url' => string] or error.
     */
    public function initiate(Order $order): array;

    /**
     * Verify a payment by transaction/payment ID.
     */
    public function verify(string $transactionId): bool;

    /**
     * Issue a full or partial refund.
     */
    public function refund(Payment $payment, float $amount): bool;
}
