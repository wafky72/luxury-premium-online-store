<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order_amount', 'max_discount', 'usage_limit', 'per_user_limit', 'used_count', 'expires_at', 'is_active', 'description'];
    protected $casts = ['expires_at' => 'datetime', 'is_active' => 'boolean', 'value' => 'float'];

    public function isValid(float $orderTotal, ?int $userId = null): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($orderTotal < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        $discount = $this->type === 'percent'
            ? $orderTotal * ($this->value / 100)
            : $this->value;

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return round(min($discount, $orderTotal), 2);
    }
}
