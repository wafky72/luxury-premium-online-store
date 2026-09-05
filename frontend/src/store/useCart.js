import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useCart = create(
  persist(
    (set, get) => ({
      cartItems: [],
      isCartOpen: false,

      openCart: () => set({ isCartOpen: true }),
      closeCart: () => set({ isCartOpen: false }),
      
      addToCart: (product, variant = null, size = null, qty = 1) => {
        const key = `${product.id}-${variant?.id || 'default'}-${size || 'default'}`;
        const items = [...get().cartItems];
        const existing = items.find(item => item.key === key);
        
        if (existing) {
          existing.quantity += qty;
          set({ cartItems: items });
        } else {
          set({
            cartItems: [
              ...items,
              {
                key,
                productId: product.id,
                product: product, // Store full product for ease of use
                variant: variant,
                size,
                quantity: qty,
              }
            ]
          });
        }
      },

      removeFromCart: (key) => {
        set({ cartItems: get().cartItems.filter(item => item.key !== key) });
      },

      updateQuantity: (key, qty) => {
        const items = [...get().cartItems];
        const item = items.find(i => i.key === key);
        if (item) {
          if (qty <= 0) {
            set({ cartItems: items.filter(i => i.key !== key) });
          } else {
            item.quantity = qty;
            set({ cartItems: items });
          }
        }
      },

      clearCart: () => set({ cartItems: [] }),
    }),
    {
      name: 'Tori Crown_cart_v2',
    }
  )
);

export const selectCartTotal = (state) => 
  state.cartItems.reduce((sum, item) => {
    const basePrice = item.product.price || 0;
    const modifier = item.variant?.priceModifier || item.variant?.price_modifier || 0;
    const price = basePrice + modifier;
    return sum + price * item.quantity;
  }, 0);

export const selectCartCount = (state) => 
  state.cartItems.reduce((sum, item) => sum + item.quantity, 0);
