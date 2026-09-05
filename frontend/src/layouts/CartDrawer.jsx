import { motion, AnimatePresence } from 'framer-motion';
import { useCart, selectCartTotal, selectCartCount } from '../store/useCart';
import { X, ShoppingBag, Plus, Minus, ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import './CartDrawer.css';

export const CartDrawer = ({ isOpen, onClose }) => {
  const navigate = useNavigate();
  const { cartItems, updateQuantity, removeFromCart } = useCart();
  const total = useCart(selectCartTotal);
  const count = useCart(selectCartCount);

  const formatPrice = (price) => {
    return `৳${price.toLocaleString('en-US')}`;
  };

  const handleCheckout = () => {
    onClose();
    navigate('/checkout');
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="tori-cart-overlay">
          <motion.div 
            className="tori-cart-backdrop"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
          />
          <motion.div 
            className="tori-cart-sheet"
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'spring', damping: 25, stiffness: 200 }}
          >
            {/* Header */}
            <div className="tori-cart-header">
              <div className="tori-cart-header__title">
                <ShoppingBag size={20} />
                <span>Your Bag ({count})</span>
              </div>
              <button className="tori-cart-close" onClick={onClose} aria-label="Close cart">
                <X size={24} />
              </button>
            </div>

            {/* Content */}
            <div className="tori-cart-content">
              {cartItems.length > 0 ? (
                <div className="tori-cart-items">
                  {cartItems.map((item) => (
                    <div className="tori-cart-item" key={item.key}>
                      <div className="tori-cart-item__img">
                        <img src={item.product.images?.[0]} alt={item.product.name} />
                      </div>
                      <div className="tori-cart-item__details">
                        <h4 className="tori-cart-item__name">{item.product.name}</h4>
                        {item.variant && <p className="tori-cart-item__variant">{item.variant.name}</p>}
                        <div className="tori-cart-item__price">
                          {formatPrice((item.product.price || 0) + (item.variant?.priceModifier || item.variant?.price_modifier || 0))}
                        </div>
                        
                        <div className="tori-cart-item__controls">
                          <div className="qty-picker">
                            <button onClick={() => updateQuantity(item.key, item.quantity - 1)}><Minus size={14} /></button>
                            <span>{item.quantity}</span>
                            <button onClick={() => updateQuantity(item.key, item.quantity + 1)}><Plus size={14} /></button>
                          </div>
                          <button 
                            className="tori-cart-item__remove" 
                            onClick={() => removeFromCart(item.key)}
                          >
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="tori-cart-empty">
                  <div className="tori-cart-empty__icon">
                    <ShoppingBag size={48} strokeWidth={1} />
                  </div>
                  <h3>Your bag is empty</h3>
                  <p>Discover our latest high jewelry collections and add your favorite pieces.</p>
                  <button className="gold-btn" onClick={onClose}>Continue Shopping</button>
                </div>
              )}
            </div>

            {/* Footer */}
            {cartItems.length > 0 && (
              <div className="tori-cart-footer">
                <div className="tori-cart-summary">
                  <div className="summary-row">
                    <span>Subtotal</span>
                    <span className="summary-total">{formatPrice(total)}</span>
                  </div>
                  <p className="summary-note">Shipping and taxes calculated at checkout.</p>
                </div>
                <button className="checkout-btn" onClick={handleCheckout}>
                  Checkout <ArrowRight size={18} />
                </button>
              </div>
            )}
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};
