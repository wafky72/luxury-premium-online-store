<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    /** 
     * Get or create a cart for the current user or session.
     */
    public function getCart(?string $sessionId = null): Cart
    {
        $userId = Auth::id();
        $sessionId = $sessionId ?? session()->getId();

        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);
            
            // Merge guest cart if sessionId provided and different from user's primary cart
            $guestCart = Cart::where('session_id', $sessionId)
                ->where('user_id', null)
                ->first();
                
            if ($guestCart && $guestCart->id !== $cart->id) {
                $this->mergeCarts($guestCart, $cart);
            }
            
            return $cart;
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add item to cart
     */
    public function addItem(?string $sessionId, ProductVariant $variant, int $qty = 1): Cart
    {
        $cart = $this->getCart($sessionId);

        DB::transaction(function () use ($cart, $variant, $qty) {
            /** @var CartItem|null $item */
            $item = $cart->items()
                ->where('product_id', $variant->product_id)
                ->where('variant_id', $variant->id)
                ->first();

            if ($item) {
                $item->increment('qty', $qty);
                $item->update(['price_snapshot' => $variant->computed_price]);
            } else {
                $cart->items()->create([
                    'product_id'     => $variant->product_id,
                    'variant_id'     => $variant->id,
                    'qty'            => $qty,
                    'price_snapshot' => $variant->computed_price,
                ]);
            }
        });

        return $cart->fresh();
    }

    /**
     * Update item quantity
     */
    public function updateItemQty(?string $sessionId, int $itemId, int $qty): Cart
    {
        $cart = $this->getCart($sessionId);
        $item = $cart->items()->findOrFail($itemId);

        if ($qty <= 0) {
            $item->delete();
        } else {
            $item->update(['qty' => $qty]);
        }

        return $cart->fresh();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(?string $sessionId, int $itemId): Cart
    {
        $cart = $this->getCart($sessionId);
        $cart->items()->where('id', $itemId)->delete();

        return $cart->fresh();
    }

    /**
     * Clear the cart
     */
    public function clear(?string $sessionId = null): void
    {
        $cart = $this->getCart($sessionId);
        $cart->items()->delete();
        $cart->update(['coupon_code' => null, 'coupon_discount' => 0]);
    }

    /**
     * Calculate subtotal
     */
    public function subtotal(Cart $cart): float
    {
        return $cart->items->sum(fn(CartItem $i) => $i->price_snapshot * $i->qty);
    }

    /**
     * Merge source cart into target cart
     */
    protected function mergeCarts(Cart $source, Cart $target): void
    {
        DB::transaction(function () use ($source, $target) {
            /** @var CartItem $item */
            foreach ($source->items as $item) {
                /** @var CartItem|null $existing */
                $existing = $target->items()
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($existing) {
                    $existing->increment('qty', $item->qty);
                } else {
                    $item->update(['cart_id' => $target->id]);
                }
            }
            $source->delete();
        });
    }
}
