/* ============================================
   API SERVICE — Laravel Backend Integration
   ============================================ */

import axios from 'axios';
import { heroData, collections, products, storyContent, trustBadges, reviews, banners } from './mockData';

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

// Get or create session ID for guest carts
const getSessionId = () => {
  let sessionId = localStorage.getItem('tory_crown_session_id');
  if (!sessionId) {
    sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    localStorage.setItem('tory_crown_session_id', sessionId);
  }
  return sessionId;
};

const apiClient = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Session-ID': getSessionId(),
  },
});

// Add a request interceptor to include the auth token
apiClient.interceptors.request.use((config) => {
  const authStorage = localStorage.getItem('tory-crown-auth');
  if (authStorage) {
    try {
      const { state } = JSON.parse(authStorage);
      if (state.token) {
        config.headers.Authorization = `Bearer ${state.token}`;
      }
    } catch (e) {
      console.error('Error parsing auth storage', e);
    }
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

/* --- Homepage / CMS --- */
export const getHeroData = async () => {
  try {
    const res = await apiClient.get('/pages/home');
    const heroSection = res.data.data.sections.find(s => s.type === 'hero_split');
    if (heroSection) return heroSection.props;
  } catch (error) {
    console.warn('Failed to fetch hero from backend, using mock', error);
  }
  return heroData;
};

export const getCollections = async () => {
  try {
    const res = await apiClient.get('/collections');
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to fetch collections, using mock', error);
  }
  return collections;
};

export const getBestSellers = async () => {
  try {
    const res = await apiClient.get('/products?featured=1');
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to fetch best sellers, using mock', error);
  }
  return products.filter(p => p.badge === 'Best Seller' || p.rating >= 4.8);
};

export const getstoryContent = async () => {
  return storyContent;
};

export const getTrustBadges = async () => {
  return trustBadges;
};

export const getBanners = async () => {
  return banners;
};

export const getReviews = async () => {
  return reviews;
};

/* --- Products --- */
export const getProducts = async (filters = {}) => {
  try {
    const params = new URLSearchParams();
    if (filters.category) params.append('category', filters.category);
    if (filters.collection) params.append('collection', filters.collection);
    if (filters.search) params.append('search', filters.search);
    if (filters.sort) params.append('sort', filters.sort);

    const res = await apiClient.get(`/products?${params.toString()}`);
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to fetch products, using mock', error);
  }

  // Fallback to mock data
  let filtered = [...products];
  if (filters.category) {
    filtered = filtered.filter(p => p.category.toLowerCase() === filters.category.toLowerCase());
  }
  if (filters.collection) {
    filtered = filtered.filter(p => p.collection.toLowerCase().includes(filters.collection.toLowerCase()));
  }
  if (filters.search) {
    const q = filters.search.toLowerCase();
    filtered = filtered.filter(p =>
      p.name.toLowerCase().includes(q) ||
      p.category.toLowerCase().includes(q) ||
      p.description.toLowerCase().includes(q)
    );
  }
  if (filters.sort === 'price-asc') filtered.sort((a, b) => a.price - b.price);
  if (filters.sort === 'price-desc') filtered.sort((a, b) => b.price - a.price);
  if (filters.sort === 'rating') filtered.sort((a, b) => b.rating - a.rating);
  return filtered;
};

export const getProductBySlug = async (slug) => {
  try {
    const res = await apiClient.get(`/products/${slug}`);
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to fetch product by slug, using mock', error);
  }
  return products.find(p => p.slug === slug) || null;
};

/* --- Orders --- */
export const getMyOrders = async () => {
  try {
    const res = await apiClient.get('/orders');
    if (res.data && res.data.data) {
      // Laravel paginate returns data in .data
      return res.data.data.data || res.data.data;
    }
    return [];
  } catch (error) {
    console.error('Failed to fetch orders:', error.response?.data || error);
    throw error.response?.data || error;
  }
};

export const createOrder = async (orderData) => {
  try {
    const res = await apiClient.post('/orders', orderData);
    return res.data;
  } catch (error) {
    console.error('Order creation failed:', error.response?.data || error);
    return { 
      success: false, 
      error: error.response?.data?.error || error.message,
      errors: error.response?.data?.errors 
    };
  }
};

/* --- Search --- */
export const searchProducts = async (query) => {
  try {
    if (!query || query.length < 2) return [];
    const res = await apiClient.post('/products/search', { q: query });
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to search products, using mock', error);
  }

  if (!query || query.length < 2) return [];
  const q = query.toLowerCase();
  return products.filter(p =>
    p.name.toLowerCase().includes(q) ||
    p.category.toLowerCase().includes(q)
  ).slice(0, 5);
};

export const getRelatedProducts = async (slug) => {
  try {
    const res = await apiClient.get(`/products/${slug}/related`);
    if (res.data && res.data.data) return res.data.data;
  } catch (error) {
    console.warn('Failed to fetch related products', error);
  }
  return products.slice(0, 4);
};

export const getProductReviews = async (slug) => {
  try {
    const res = await apiClient.get(`/products/${slug}/reviews`);
    if (res.data && res.data.data) return res.data.data.data || res.data.data;
  } catch (error) {
    console.warn('Failed to fetch product reviews', error);
  }
  return reviews.slice(0, 3);
};

/* --- Auth --- */
export const login = async (credentials) => {
  try {
    const res = await apiClient.post('/auth/login', credentials);
    return res.data;
  } catch (error) {
    console.error('Login failed:', error.response?.data || error);
    throw error.response?.data || error;
  }
};

export const register = async (userData) => {
  try {
    const res = await apiClient.post('/auth/register', userData);
    return res.data;
  } catch (error) {
    console.error('Registration failed:', error.response?.data || error);
    throw error.response?.data || error;
  }
};

export const logout = async () => {
  try {
    await apiClient.post('/auth/logout');
  } catch (error) {
    console.error('Logout failed:', error.response?.data || error);
  }
};

export const getMe = async () => {
  try {
    const res = await apiClient.get('/auth/me');
    return res.data;
  } catch (error) {
    console.error('Failed to fetch current user:', error.response?.data || error);
    throw error.response?.data || error;
  }
};
