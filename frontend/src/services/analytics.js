import { v4 as uuidv4 } from 'uuid';

/**
 * Pushes event to dataLayer and returns the generated event_id
 * so it can be sent to the backend for Facebook CAPI deduplication.
 */
export const pushToDataLayer = (eventName, eventData) => {
  const eventId = uuidv4();
  
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: eventName,
    event_id: eventId,
    ...eventData,
  });

  // Example of sending to Laravel backend for CAPI processing
  // fetch('http://localhost:8000/api/tracking/capi', {
  //   method: 'POST',
  //   headers: { 'Content-Type': 'application/json' },
  //   body: JSON.stringify({ eventName, eventId, eventData })
  // }).catch(e => console.warn('CAPI sync failed', e));

  return eventId;
};

export const AnalyticsService = {
  trackViewContent: (product) => {
    return pushToDataLayer('ViewContent', {
      ecommerce: {
        currency: 'BDT',
        value: product.price,
        items: [{
          item_id: product.sku || product.id,
          item_name: product.name,
          price: product.price,
          item_category: product.category,
        }]
      }
    });
  },

  trackAddToCart: (product, variant, quantity = 1) => {
    return pushToDataLayer('AddToCart', {
      ecommerce: {
        currency: 'BDT',
        value: product.price * quantity,
        items: [{
          item_id: product.sku || product.id,
          item_name: product.name,
          price: product.price,
          item_variant: variant?.name || 'Default',
          quantity: quantity
        }]
      }
    });
  },

  trackInitiateCheckout: (cartItems, totalValue) => {
    return pushToDataLayer('InitiateCheckout', {
      ecommerce: {
        currency: 'BDT',
        value: totalValue,
        items: cartItems.map(item => ({
          item_id: item.product.sku || item.product.id,
          item_name: item.product.name,
          price: item.product.price,
          item_variant: item.variant?.name,
          quantity: item.quantity
        }))
      }
    });
  },

  trackPurchase: (orderId, cartItems, totalValue) => {
    return pushToDataLayer('Purchase', {
      ecommerce: {
        transaction_id: orderId,
        currency: 'BDT',
        value: totalValue,
        items: cartItems.map(item => ({
          item_id: item.product.sku || item.product.id,
          item_name: item.product.name,
          price: item.product.price,
          item_variant: item.variant?.name,
          quantity: item.quantity
        }))
      }
    });
  }
};
