<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'name', 'karat', 'size', 'color',
        'weight_grams', 'stone_type', 'stone_weight',
        'making_charge', 'base_price_override', 'tag_price',
        'stock_qty', 'reserved_qty', 'is_active',
    ];

    protected $casts = [
        'weight_grams'        => 'float',
        'stone_weight'        => 'float',
        'making_charge'       => 'float',
        'base_price_override' => 'float',
        'is_active'           => 'boolean',
    ];

    protected $appends = ['price_modifier'];

    public function getPriceModifierAttribute(): float
    {
        $wasLoaded = $this->relationLoaded('product');
        $productPrice = $this->product->price ?? 0;
        
        // Prevent infinite recursion in toArray() by not holding onto the product relation 
        // if it wasn't explicitly eager loaded.
        if (!$wasLoaded) {
            $this->unsetRelation('product');
        }

        if ($productPrice === 0) return 0;
        return $this->computed_price - $productPrice;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    /**
     * Real-time price: (gold_rate × weight) + making_charge + 5% VAT
     * Falls back to base_price_override if set.
     */
    public function getComputedPriceAttribute(): float
    {
        if ($this->base_price_override) {
            return round($this->base_price_override, 2);
        }

        $rate = GoldRate::latestFor($this->karat ?? '22K');
        if (! $rate || ! $this->weight_grams) {
            return round($this->making_charge ?? 0, 2);
        }

        $base = $rate->price_per_gram * $this->weight_grams;
        $total = $base + ($this->making_charge ?? 0);
        return round($total * 1.05, 2); // +5% VAT
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_qty - $this->reserved_qty);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Atomic stock reservation — prevents overselling.
     */
    public function reserveStock(int $qty): bool
    {
        return (bool) static::query()
            ->where('id', $this->id)
            ->whereRaw('(stock_qty - reserved_qty) >= ?', [$qty])
            ->update(['reserved_qty' => DB::raw("reserved_qty + {$qty}")]);
    }

    public function releaseStock(int $qty): void
    {
        $this->decrement('reserved_qty', min($qty, $this->reserved_qty));
    }

    public function confirmStock(int $qty): void
    {
        $this->decrement('stock_qty', $qty);
        $this->decrement('reserved_qty', min($qty, $this->reserved_qty));
    }
}
