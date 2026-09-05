import { Link, useLocation } from 'react-router-dom';
import { Home, Grid, Heart, ShoppingBag, User } from 'lucide-react';
import { useCart } from '../store/useCart';
import { useWishlist } from '../store/useWishlist';
import './BottomNav.css';

export const BottomNav = () => {
  const location = useLocation();
  const { cartCount } = useCart();
  const { wishlistCount } = useWishlist();

  const isActive = (path) => location.pathname === path;

  return (
    <nav className="bottom-nav">
      <Link to="/" className={`bottom-nav__item ${isActive('/') ? 'is-active' : ''}`}>
        <Home size={20} strokeWidth={isActive('/') ? 2 : 1.5} />
        <span>Home</span>
      </Link>
      
      <Link to="/collections" className={`bottom-nav__item ${isActive('/collections') ? 'is-active' : ''}`}>
        <Grid size={20} strokeWidth={isActive('/collections') ? 2 : 1.5} />
        <span>Shop</span>
      </Link>

      <Link to="/wishlist" className={`bottom-nav__item ${isActive('/wishlist') ? 'is-active' : ''}`}>
        <div className="bottom-nav__icon-wrap">
          <Heart size={20} strokeWidth={isActive('/wishlist') ? 2 : 1.5} />
          {wishlistCount > 0 && <span className="bottom-nav__badge">{wishlistCount}</span>}
        </div>
        <span>Wishlist</span>
      </Link>

      <Link to="/checkout" className={`bottom-nav__item ${isActive('/checkout') ? 'is-active' : ''}`}>
        <div className="bottom-nav__icon-wrap">
          <ShoppingBag size={20} strokeWidth={isActive('/checkout') ? 2 : 1.5} />
          {cartCount > 0 && <span className="bottom-nav__badge">{cartCount}</span>}
        </div>
        <span>Bag</span>
      </Link>

      <Link to="/login" className={`bottom-nav__item ${isActive('/profile') || isActive('/login') ? 'is-active' : ''}`}>
        <User size={20} strokeWidth={isActive('/profile') || isActive('/login') ? 2 : 1.5} />
        <span>Me</span>
      </Link>
    </nav>
  );
};
