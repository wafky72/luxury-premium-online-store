import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { useQuery } from '@tanstack/react-query';
import { getProductBySlug, getRelatedProducts, getProductReviews } from '../services/api';
import { useCart } from '../store/useCart';
import { useWishlist } from '../store/useWishlist';
import { AnalyticsService } from '../services/analytics';
import { Button } from '../ui/Button';
import { ProductCard } from '../ui/ProductCard';
import { Loader } from '../ui/Loader';
import { Heart, ChevronRight, Star, Truck, Shield } from 'lucide-react';
import './ProductDetails.css';

export const ProductDetails = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { addToCart, openCart } = useCart();
  const { toggleWishlist, isInWishlist } = useWishlist();

  // Selection state
  const [activeImage, setActiveImage] = useState(0);
  const [selectedVariant, setSelectedVariant] = useState(null);
  const [selectedSize, setSelectedSize] = useState('');
  const [activeTab, setActiveTab] = useState('description');

  const { data: product, isLoading, isError } = useQuery({
    queryKey: ['product', slug],
    queryFn: async () => {
      const prod = await getProductBySlug(slug);
      if (prod) {
        AnalyticsService.trackViewContent(prod);
      }
      return prod;
    },
  });

  const { data: related = [] } = useQuery({
    queryKey: ['related', product?.id],
    queryFn: () => getRelatedProducts(slug),
    enabled: !!product?.id,
  });

  const { data: reviews = [] } = useQuery({
    queryKey: ['reviews', product?.id],
    queryFn: () => getProductReviews(slug),
    enabled: !!product?.id,
  });

  // Initialize selection when product data arrives
  if (product && !selectedVariant && product.variants?.length > 0) {
    setSelectedVariant(product.variants[0]);
  }
  if (product && !selectedSize && product.sizes?.length > 0) {
    setSelectedSize(product.sizes[0]);
  }

  useEffect(() => {
    if (product) {
      window.scrollTo(0, 0);
    }
  }, [product]);

  useEffect(() => {
    if (isError) navigate('/not-found');
  }, [isError, navigate]);


  if (isLoading || !product) {
    return <Loader />;
  }

  const images = product.images || [];
  const specs = product.specs || {};
  const variants = product.variants || [];
  const sizes = product.sizes || [];

  // Determine stock availability from product variants or fallback to legacy attribute
  const isInStock = selectedVariant 
    ? (selectedVariant.stock_qty > 0) 
    : (variants.some(v => v.stock_qty > 0) || product.inStock === true);

  const wished = isInWishlist(product.id);
  const currentPrice = (product.price || 0) + (selectedVariant?.priceModifier || 0);

  const formatPrice = (price) => {
    if (isNaN(price)) return 'Price on Request';
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'BDT',
      minimumFractionDigits: 0,
    }).format(price);
  };

  const handleAddToCart = () => {
    addToCart(product, selectedVariant, selectedSize, 1);
    AnalyticsService.trackAddToCart(product, selectedVariant, 1);
    openCart();
  };

  const handleBuyNow = () => {
    handleAddToCart();
    navigate('/checkout');
  };

  return (
    <div className="product-page page-enter-active">
      
      {/* Breadcrumbs */}
      <div className="container">
        <nav className="breadcrumbs">
          <span onClick={() => navigate('/')}>Home</span>
          <ChevronRight size={14} />
          <span onClick={() => navigate('/collections')}>Collections</span>
          <ChevronRight size={14} />
          <span className="current">{product.name}</span>
        </nav>
      </div>

      <section className="product-main container">
        {/* Left: Gallery */}
        <div className="product-gallery">
          <div className="product-gallery__main">
            <AnimatePresence mode="wait">
              <motion.img 
                key={activeImage}
                src={typeof images[activeImage] === 'string' ? images[activeImage] : (images[activeImage]?.url || 'https://via.placeholder.com/800')} 
                alt={product.name}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.3 }}
              />
            </AnimatePresence>
            <button 
              className={`product-wishlist-btn ${wished ? 'is-active' : ''}`}
              onClick={() => toggleWishlist(product)}
            >
              <Heart size={24} fill={wished ? "currentColor" : "none"} strokeWidth={1.5} />
            </button>
          </div>
          <div className="product-gallery__thumbs">
            {images.map((img, idx) => (
              <button 
                key={idx} 
                className={`thumb-btn ${activeImage === idx ? 'is-active' : ''}`}
                onClick={() => setActiveImage(idx)}
              >
                <img src={typeof img === 'string' ? img : (img?.url || 'https://via.placeholder.com/100')} alt={`Thumbnail ${idx + 1}`} />
              </button>
            ))}
          </div>
        </div>

        {/* Right: Info */}
        <div className="product-info">
          <div className="product-info__header">
            <span className="product-collection">
              {typeof product.collection === 'string' ? product.collection : (product.collection?.name || product.collection_name || '')}
            </span>
            <h1 className="product-title">{product.name}</h1>
            <div className="product-price-wrap">
              <span className="product-price">{formatPrice(currentPrice)}</span>
              {product.originalPrice && (
                <span className="product-price-original">{formatPrice(product.originalPrice)}</span>
              )}
            </div>
          </div>

          <div className="product-divider" />

          {/* Variants */}
          {variants.length > 0 && (
            <div className="product-variants">
              <span className="variant-label">Material: <strong>{selectedVariant?.name}</strong></span>
              <div className="variant-options">
                {variants.map(variant => (
                  <button
                    key={variant.id}
                    className={`variant-btn ${selectedVariant?.id === variant.id ? 'is-active' : ''}`}
                    onClick={() => setSelectedVariant(variant)}
                    style={{ backgroundColor: variant.color }}
                    aria-label={variant.name}
                  />
                ))}
              </div>
            </div>
          )}

          {/* Sizes */}
          {sizes.length > 0 && (
            <div className="product-sizes">
              <div className="size-header">
                <span className="variant-label">Size</span>
                <button className="size-guide-btn">Size Guide</button>
              </div>
              <div className="size-options">
                {sizes.map(size => (
                  <button
                    key={size}
                    className={`size-btn ${selectedSize === size ? 'is-active' : ''}`}
                    onClick={() => setSelectedSize(size)}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Add to Cart - Sticky on mobile */}
          <div className="product-actions" style={{ display: 'flex', gap: '16px' }}>
            <Button 
              size="lg" 
              variant="outline"
              style={{ flex: 1 }}
              onClick={handleAddToCart}
              disabled={!isInStock}
            >
              Add to Cart
            </Button>
            <Button 
              size="lg" 
              variant="primary"
              style={{ flex: 1 }}
              onClick={handleBuyNow}
              disabled={!isInStock}
            >
              Buy Now
            </Button>
          </div>

          {/* Trust Highlights */}
          <div className="product-trust">
            <div className="trust-item">
              <Truck size={20} strokeWidth={1.5} />
              <span>Complimentary insured shipping</span>
            </div>
            <div className="trust-item">
              <Shield size={20} strokeWidth={1.5} />
              <span>Certificate of authenticity included</span>
            </div>
          </div>
        </div>
      </section>

      {/* Details Tabs */}
      <section className="product-tabs container">
        <div className="tabs-header">
          <button 
            className={`tab-btn ${activeTab === 'description' ? 'is-active' : ''}`}
            onClick={() => setActiveTab('description')}
          >
            Description
          </button>
          <button 
            className={`tab-btn ${activeTab === 'specs' ? 'is-active' : ''}`}
            onClick={() => setActiveTab('specs')}
          >
            Specifications
          </button>
          <button 
            className={`tab-btn ${activeTab === 'reviews' ? 'is-active' : ''}`}
            onClick={() => setActiveTab('reviews')}
          >
            Reviews ({product.reviews})
          </button>
        </div>

        <div className="tabs-content">
          <AnimatePresence mode="wait">
            {activeTab === 'description' && (
              <motion.div 
                key="desc"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -10 }}
                className="tab-pane"
              >
                <p>{product.description}</p>
              </motion.div>
            )}

            {activeTab === 'specs' && (
              <motion.div 
                key="specs"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -10 }}
                className="tab-pane"
              >
                <ul className="specs-list">
                  {Object.entries(specs).map(([key, value]) => (
                    <li key={key}>
                      <span className="spec-key">{key}</span>
                      <span className="spec-value">{value}</span>
                    </li>
                  ))}
                </ul>
              </motion.div>
            )}

            {activeTab === 'reviews' && (
              <motion.div 
                key="revs"
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -10 }}
                className="tab-pane"
              >
                <div className="reviews-list">
                  {reviews.length > 0 ? reviews.map(rev => (
                    <div key={rev.id} className="review-item">
                      <div className="review-header">
                        <span className="review-author">{rev.author} {rev.verified && <span className="verified-badge">Verified Buyer</span>}</span>
                        <div className="review-stars">
                          {[...Array(5)].map((_, i) => (
                            <Star key={i} size={14} fill={i < rev.rating ? "#D4AF37" : "none"} color={i < rev.rating ? "#D4AF37" : "#E8E4DD"} />
                          ))}
                        </div>
                      </div>
                      <p className="review-text">{rev.text}</p>
                      <span className="review-date">{new Date(rev.date).toLocaleDateString()}</span>
                    </div>
                  )) : (
                    <p>No reviews yet.</p>
                  )}
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </section>

      {/* Related Products */}
      {related.length > 0 && (
        <section className="section bg-warm related-products">
          <div className="container">
            <h2 className="section-title text-center" style={{marginBottom: 'var(--space-3xl)'}}>You May Also Like</h2>
            <div className="collections-grid" style={{gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))'}}>
              {related.map(prod => (
                <ProductCard key={prod.id} product={prod} />
              ))}
            </div>
          </div>
        </section>
      )}

    </div>
  );
};
