import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useCart, selectCartTotal } from '../store/useCart';
import { Button } from '../ui/Button';
import { AnalyticsService } from '../services/analytics';
import './Checkout.css';
import { createOrder } from '../services/api';

import { OrderSuccessModal } from '../components/OrderSuccessModal';

const DELIVERY_RATES = {
  dhaka: 80,
  outside_dhaka: 150
};

export const Checkout = () => {
  const navigate = useNavigate();
  const cartItems = useCart(state => state.cartItems);
  const cartTotal = useCart(selectCartTotal);
  const clearCart = useCart(state => state.clearCart);
  
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    phone: '',
    email: '',
    address: '',
    city: 'Dhaka', // Capitalized for backend matching
    zone: ''
  });

  const [paymentMethod, setPaymentMethod] = useState('cod');
  const [isProcessing, setIsProcessing] = useState(false);
  const [successOrder, setSuccessOrder] = useState(null);

  // Track InitiateCheckout on load
  useEffect(() => {
    if (cartItems.length > 0) {
      AnalyticsService.trackInitiateCheckout(cartItems, cartTotal);
    }
  }, [cartItems, cartTotal]);

  const deliveryCharge = formData.city.toLowerCase() === 'dhaka' ? DELIVERY_RATES.dhaka : DELIVERY_RATES.outside_dhaka;
  const vatAmount = cartTotal * 0.05; // 5% VAT simulation
  const finalTotal = cartTotal + deliveryCharge + vatAmount;

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handlePlaceOrder = async (e) => {
    e.preventDefault();
    if (cartItems.length === 0) return;

    setIsProcessing(true);

    const orderPayload = {
      name: `${formData.firstName} ${formData.lastName}`,
      email: formData.email,
      phone: formData.phone,
      address: formData.address,
      city: formData.city,
      district: formData.city,
      payment_method: paymentMethod,
      items: cartItems.map(item => ({
        variant_id: item.variant?.id || 1,
        quantity: item.quantity
      }))
    };

    const response = await createOrder(orderPayload);

    if (response.success) {
      if (response.payment_result?.status === 'redirect') {
        window.location.href = response.payment_result.redirect_url;
        return;
      }

      setSuccessOrder(response.data);
      // Wait for modal close before clearing cart and navigating
    } else {
      console.error('Order Error:', response);
      const errorMsg = response.error === 'VALIDATION_ERROR' 
        ? JSON.stringify(response.errors) 
        : (response.message || response.error);
      alert(`Checkout Failed: ${errorMsg || 'Something went wrong.'}`);
    }
    
    setIsProcessing(false);
  };

  if (cartItems.length === 0) {
    return (
      <div className="checkout-page" style={{ textAlign: 'center', paddingTop: '20vh' }}>
        <h2>Your Cart is Empty</h2>
        <p style={{ marginTop: '20px', marginBottom: '40px' }}>Add some luxury pieces to your cart to proceed.</p>
        <Button onClick={() => navigate('/')}>Return to Store</Button>
      </div>
    );
  }

  return (
    <div className="checkout-page">
      <div className="checkout-grid">
        <motion.div 
          className="checkout-form-container"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <form onSubmit={handlePlaceOrder}>
            <div className="checkout-section">
              <h2>Contact Information</h2>
              <div className="checkout-form-row">
                <div className="checkout-form-group">
                  <label>First Name</label>
                  <input required name="firstName" value={formData.firstName} onChange={handleInputChange} />
                </div>
                <div className="checkout-form-group">
                  <label>Last Name</label>
                  <input required name="lastName" value={formData.lastName} onChange={handleInputChange} />
                </div>
              </div>
              <div className="checkout-form-row">
                <div className="checkout-form-group">
                  <label>Phone Number (BD)</label>
                  <input required type="tel" name="phone" value={formData.phone} onChange={handleInputChange} placeholder="01XXX-XXXXXX" />
                </div>
                <div className="checkout-form-group">
                  <label>Email Address</label>
                  <input required type="email" name="email" value={formData.email} onChange={handleInputChange} />
                </div>
              </div>
            </div>

            <div className="checkout-section">
              <h2>Delivery Details</h2>
              <div className="checkout-form-group">
                <label>Delivery Area</label>
                <select name="city" value={formData.city} onChange={handleInputChange}>
                  <option value="dhaka">Inside Dhaka</option>
                  <option value="outside_dhaka">Outside Dhaka</option>
                </select>
              </div>
              <div className="checkout-form-group">
                <label>Detailed Address</label>
                <input required name="address" value={formData.address} onChange={handleInputChange} placeholder="House, Road, Block, Area" />
              </div>
            </div>

            <div className="checkout-section">
              <h2>Payment Method</h2>
              <div className="method-selector">
                <div className={`method-option ${paymentMethod === 'cod' ? 'selected' : ''}`} onClick={() => setPaymentMethod('cod')}>
                  <div className="method-info">
                    <div className="method-radio"></div>
                    <span>Cash on Delivery (COD)</span>
                  </div>
                  {paymentMethod === 'cod' && <span style={{ fontSize: '0.85rem', color: 'var(--color-text-light)' }}>Pay when you receive.</span>}
                </div>
                <div className={`method-option ${paymentMethod === 'bkash' ? 'selected' : ''}`} onClick={() => setPaymentMethod('bkash')}>
                  <div className="method-info">
                    <div className="method-radio"></div>
                    <span>bKash</span>
                  </div>
                </div>
                <div className={`method-option ${paymentMethod === 'nagad' ? 'selected' : ''}`} onClick={() => setPaymentMethod('nagad')}>
                  <div className="method-info">
                    <div className="method-radio"></div>
                    <span>Nagad</span>
                  </div>
                </div>
                <div className={`method-option ${paymentMethod === 'sslcommerz' ? 'selected' : ''}`} onClick={() => setPaymentMethod('sslcommerz')}>
                  <div className="method-info">
                    <div className="method-radio"></div>
                    <span>Card / Net Banking (SSLCommerz)</span>
                  </div>
                </div>
              </div>
            </div>

            <Button type="submit" variant="primary" style={{ width: '100%', marginTop: '20px' }} isLoading={isProcessing}>
              {paymentMethod === 'cod' ? 'Confirm Order' : 'Proceed to Payment'}
            </Button>
          </form>
        </motion.div>

        <motion.div 
          className="order-summary"
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
        >
          <h3 style={{ fontFamily: 'var(--font-heading)', fontSize: '1.5rem', marginBottom: '24px' }}>Order Summary</h3>
          
          <div className="summary-items">
            {cartItems.map((item) => (
              <div className="summary-item" key={item.key}>
                <img src={item.product.images[0]} alt={item.product.name} className="summary-item-img" />
                <div className="summary-item-details">
                  <h4 className="summary-item-name">{item.product.name}</h4>
                  {item.variant && <p className="summary-item-variant">{item.variant.name}</p>}
                  <p className="summary-item-qty">Qty: {item.quantity}</p>
                </div>
                <div className="summary-item-price">
                  ৳{(((item.product.price || 0) + (item.variant?.priceModifier || item.variant?.price_modifier || 0)) * item.quantity).toLocaleString('en-US')}
                </div>
              </div>
            ))}
          </div>

          <div className="summary-totals">
            <div className="summary-row">
              <span>Subtotal</span>
              <span>৳{cartTotal.toLocaleString('en-US')}</span>
            </div>
            <div className="summary-row">
              <span>Estimated VAT (5%)</span>
              <span>৳{vatAmount.toLocaleString('en-US')}</span>
            </div>
            <div className="summary-row">
              <span>Delivery Charge</span>
              <span>৳{deliveryCharge.toLocaleString('en-US')}</span>
            </div>
            <div className="summary-row total">
              <span>Total</span>
              <span>৳{finalTotal.toLocaleString('en-US')}</span>
            </div>
          </div>
        </motion.div>
      </div>

      {successOrder && (
        <OrderSuccessModal 
          order={successOrder} 
          onClose={() => {
            clearCart();
            navigate('/', { state: { orderSuccess: true, orderId: successOrder.order_number } });
            window.scrollTo(0, 0);
          }} 
        />
      )}
    </div>
  );
};
