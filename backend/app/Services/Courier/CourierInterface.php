<?php

namespace App\Services\Courier;

use App\Models\Order;

interface CourierInterface
{
    public function createConsignment(Order $order): array;
    public function getStatus(string $consignmentId): array;
    public function handleWebhook(array $payload): void;
}
