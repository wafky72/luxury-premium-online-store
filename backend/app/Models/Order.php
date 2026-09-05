<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'coupon_id',
        'status', 'recipient_name', 'recipient_phone',
        'shipping_address', 'shipping_city', 'shipping_district',
        'subtotal', 'shipping_fee', 'coupon_discount', 'vat', 'total',
        'payment_method', 'payment_status', 'advance_paid',
        'courier', 'courier_service', 'courier_charge',
        'fraud_score', 'fraud_data', 'notes', 'source', 'tenant_id',
    ];

    protected $casts = [
        'subtotal'        => 'float',
        'shipping_fee'    => 'float',
        'coupon_discount' => 'float',
        'vat'             => 'float',
        'total'           => 'float',
        'advance_paid'    => 'float',
        'courier_charge'  => 'float',
        'fraud_data'      => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->order_number ??= static::generateOrderNumber();
        });

        static::updated(function (self $order) {
            if ($order->isDirty('status')) {
                $order->timeline()->create([
                    'status'     => $order->status,
                    'note'       => "Status changed to {$order->status}",
                    'created_by' => 'system',
                    'created_at' => now(),
                ]);
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'TC-' . date('Y');
        $last = static::where('order_number', 'like', "{$prefix}-%")
            ->orderByDesc('id')->value('order_number');
        $seq = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    // --- Relationships ---

    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function coupon(): BelongsTo    { return $this->belongsTo(Coupon::class); }
    public function items(): HasMany       { return $this->hasMany(OrderItem::class); }
    public function timeline(): HasMany    { return $this->hasMany(OrderTimeline::class)->orderBy('created_at'); }
    public function payments(): HasMany    { return $this->hasMany(Payment::class); }
    public function shipment(): HasOne     { return $this->hasOne(Shipment::class); }

    // --- Scopes ---

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeActive($q)    { return $q->whereNotIn('status', ['cancelled', 'returned', 'refunded']); }
    public function scopeFlagged($q)   { return $q->where('fraud_score', '>', 70); }

    // --- Helpers ---

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
