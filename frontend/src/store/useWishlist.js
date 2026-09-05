import { useState, useCallback, useEffect } from 'react';

const WISHLIST_KEY = 'Tori Crown_wishlist';

const getStoredWishlist = () => {
  try {
    const data = JSON.parse(localStorage.getItem(WISHLIST_KEY)) || [];
    // Normalize data for consistency (handle legacy items with single 'image' property)
    return data.map(item => ({
      ...item,
      images: Array.isArray(item.images) ? item.images : (item.image ? [item.image] : [])
    }));
  } catch { return []; }
};

let listeners = [];
let wishlistState = getStoredWishlist();

const notify = () => {
  localStorage.setItem(WISHLIST_KEY, JSON.stringify(wishlistState));
  listeners.forEach(fn => fn(wishlistState));
};

export const useWishlist = () => {
  const [wishlist, setWishlist] = useState(wishlistState);

  useEffect(() => {
    const handler = (newWishlist) => setWishlist([...newWishlist]);
    listeners.push(handler);
    return () => { listeners = listeners.filter(l => l !== handler); };
  }, []);

  const toggleWishlist = useCallback((product) => {
    const isWished = wishlistState.some(item => item.id === product.id);
    if (isWished) {
      wishlistState = wishlistState.filter(item => item.id !== product.id);
    } else {
      // Store relevant product data, ensuring images is an array as expected by ProductCard
      wishlistState.push({
        ...product,
        // Ensure images is an array even if it was just a single string in some older data
        images: Array.isArray(product.images) ? product.images : [product.images || product.image],
      });
    }
    notify();
  }, []);

  const removeFromWishlist = useCallback((productId) => {
    wishlistState = wishlistState.filter(item => item.id !== productId);
    notify();
  }, []);

  const clearWishlist = useCallback(() => {
    wishlistState = [];
    notify();
  }, []);

  const isInWishlist = useCallback((productId) => {
    return wishlist.some(item => item.id === productId);
  }, [wishlist]);

  return { 
    wishlist, 
    toggleWishlist, 
    removeFromWishlist, 
    clearWishlist, 
    isInWishlist, 
    wishlistCount: wishlist.length 
  };
};
