import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';

// Get backend API URL from the existing api service config if possible
const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

// Session ID for deduplication
let sessionId = localStorage.getItem('tory_session_id');
if (!sessionId) {
  sessionId = uuidv4();
  localStorage.setItem('tory_session_id', sessionId);
}

/**
 * Fire an event to GTM, Meta Pixel, and Server-Side CAPI
 */
export const trackEvent = async (eventName, payload = {}) => {
  try {
    const eventId = uuidv4();

    // 1. Fire to Google Tag Manager (DataLayer)
    if (typeof window !== 'undefined' && window.dataLayer) {
      window.dataLayer.push({
        event: eventName,
        event_id: eventId,
        ...payload
      });
    }

    // 2. Fire to Meta Pixel (Frontend)
    if (typeof window !== 'undefined' && window.fbq) {
      let fbqEventName = eventName;
      // Map standard ecommerce events to FB standard events
      if (eventName === 'view_item') fbqEventName = 'ViewContent';
      if (eventName === 'add_to_cart') fbqEventName = 'AddToCart';
      if (eventName === 'purchase') fbqEventName = 'Purchase';
      if (eventName === 'begin_checkout') fbqEventName = 'InitiateCheckout';

      window.fbq('track', fbqEventName, payload, { eventID: eventId });
    }

    // 3. Fire to Laravel Backend (for CAPI / Server-Side tracking)
    await axios.post(`${API_BASE}/events`, {
      event_name: eventName,
      product_id: payload.content_ids ? payload.content_ids[0] : null,
      value: payload.value,
      payload: payload,
      source: 'website'
    }, {
      headers: {
        'X-Session-ID': sessionId,
        'Accept': 'application/json'
      }
    });

  } catch (err) {
    console.error('Failed to track event:', err);
  }
};
