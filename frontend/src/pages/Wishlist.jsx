import { motion, AnimatePresence } from 'framer-motion';
import { useWishlist } from '../store/useWishlist';
import { useCart } from '../store/useCart';
import { ProductCard } from '../ui/ProductCard';
import { Heart, ShoppingBag, Trash2, ArrowRight, ChevronRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import './Wishlist.css';

export const Wishlist = () => {
  const { wishlist, removeFromWishlist, clearWishlist } = useWishlist();
  const { addToCart, openCart } = useCart();

  const handleMoveToCart = (product) => {
    addToCart(product);
    removeFromWishlist(product.id);
    openCart();
  };

  const handleMoveAllToCart = () => {
    wishlist.forEach(product => {
      addToCart(product);
    });
    clearWishlist();
    openCart();
  };

  return (
    <div className="wishlist-page">
      <nav className="breadcrumb">
        <div className="container">
          <Link to="/">Home</Link>
          <ChevronRight size={14} />
          <span>Wishlist</span>
        </div>
      </nav>

      <header className="wishlist-header">
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
          >
            <span className="wishlist-header__label">Your Selection</span>
            <h1 className="wishlist-header__title">Private Collection</h1>
            <div className="gold-divider-center" />
            <p className="wishlist-header__desc">
              Discover and curate your personal anthology of Tori Crown jewelry. 
              These exquisite pieces are saved for your consideration.
            </p>
          </motion.div>
        </div>
      </header>

      <div className="container">
        {wishlist.length > 0 ? (
          <div className="wishlist-content">
            <div className="wishlist-toolbar">
              <div className="wishlist-info">
                <span className="wishlist-count">{wishlist.length} {wishlist.length === 1 ? 'Piece' : 'Pieces'}</span>
              </div>
              <div className="wishlist-toolbar__actions">
                <button className="toolbar-btn" onClick={handleMoveAllToCart}>
                  <ShoppingBag size={16} />
                  Move All to Bag
                </button>
                <button className="toolbar-btn toolbar-btn--text" onClick={clearWishlist}>
                  Clear All
                </button>
              </div>
            </div>

            <div className="wishlist-grid">
              <AnimatePresence mode="popLayout">
                {wishlist.map((product, index) => (
                  <motion.div 
                    key={product.id}
                    layout
                    initial={{ opacity: 0, scale: 0.9, y: 20 }}
                    animate={{ opacity: 1, scale: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.8 }}
                    transition={{ delay: index * 0.05 }}
                    className="wishlist-item"
                  >
                    <ProductCard product={product} />
                    <div className="wishlist-item__actions">
                      <button 
                        className="wishlist-btn-primary"
                        onClick={() => handleMoveToCart(product)}
                      >
                        <ShoppingBag size={18} />
                        <span>Move to Bag</span>
                      </button>
                      <button 
                        className="wishlist-btn-icon"
                        onClick={() => removeFromWishlist(product.id)}
                        title="Remove from collection"
                      >
                        <Trash2 size={18} />
                      </button>
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          </div>
        ) : (
          <motion.div 
            className="wishlist-empty"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 1 }}
          >
            <div className="wishlist-empty__visual">
              <div className="wishlist-empty__circle">
                <Heart size={48} strokeWidth={1} />
              </div>
            </div>
            <h2 className="wishlist-empty__title">Your collection is empty</h2>
            <p className="wishlist-empty__text">
              Begin your journey into high jewelry by exploring our curated collections.
            </p>
            <Link to="/collections" className="gold-btn">
              Explore Collections <ArrowRight size={18} />
            </Link>
          </motion.div>
        )}
      </div>
    </div>
  );
};
