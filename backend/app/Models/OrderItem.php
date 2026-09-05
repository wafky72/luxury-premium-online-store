<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'product_name',
        'variant_name', 'sku', 'karat', 'weight_grams',
        'image_url', 'qty', 'unit_price', 'total_price'
    ];

    protected $casts = [
        'weight_grams' => 'float',
        'qty' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
