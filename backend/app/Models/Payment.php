<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'gateway', 'transaction_id', 'gateway_order_id',
        'amount', 'status', 'gateway_response', 'payment_method_detail',
        'verified_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'gateway_response' => 'array',
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
