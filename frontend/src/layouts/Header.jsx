import { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { ShoppingBag, Heart, Search, Menu, X, User } from 'lucide-react';
import { useCart, selectCartCount } from '../store/useCart';
import { useWishlist } from '../store/useWishlist';
import { useAuth } from '../store/useAuth';
import './Header.css';

export const Header = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const location = useLocation();
  const cartCount = useCart(selectCartCount);
  const openCart = useCart(state => state.openCart);
  const { wishlistCount } = useWishlist();
  const isAuthenticated = useAuth(state => state.isAuthenticated);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const [prevPath, setPrevPath] = useState(location.pathname);

  if (location.pathname !== prevPath) {
    setPrevPath(location.pathname);
    setIsMobileMenuOpen(false);
  }


  return (
    <>
      <header className={`tori-header ${isScrolled || location.pathname !== '/' ? 'is-scrolled' : ''}`}>
        <div className="container tori-header__inner">
          
          <button 
            className="tori-header__menu-btn"
            onClick={() => setIsMobileMenuOpen(true)}
            aria-label="Open menu"
          >
            <Menu size={24} strokeWidth={1.5} />
          </button>

          <Link to="/" className="tori-header__logo">
            <img src="/logo.png" alt="Tori Crown" className="tori-header__logo-img" />
            <span className="tori-header__logo-text">Tori Crown</span>
          </Link>

          <nav className="tori-header__nav">
            <Link to="/collections" className="tori-header__link">Collections</Link>
            <Link to="/new-arrivals" className="tori-header__link">New Arrivals</Link>
            <Link to="/bridal" className="tori-header__link">Bridal</Link>
            <Link to="/gifts" className="tori-header__link">Gifts</Link>
            <Link to="/about" className="tori-header__link">About</Link>
          </nav>

          <div className="tori-header__actions">
            <button className="tori-header__action-btn" aria-label="Search">
              <Search size={20} strokeWidth={1.5} />
            </button>
            <Link to={isAuthenticated ? "/profile" : "/login"} className="tori-header__action-btn" aria-label="Account">
              <User size={20} strokeWidth={1.5} />
            </Link>
            <Link to="/wishlist" className="tori-header__action-btn" aria-label="Wishlist">
              <Heart size={20} strokeWidth={1.5} />
              {wishlistCount > 0 && <span className="tori-header__badge">{wishlistCount}</span>}
            </Link>
            <button onClick={openCart} className="tori-header__action-btn" aria-label="Cart">
              <ShoppingBag size={20} strokeWidth={1.5} />
              {cartCount > 0 && <span className="tori-header__badge">{cartCount}</span>}
            </button>
          </div>
        </div>
      </header>

      {/* Mobile Menu Overlay */}
      <div className={`tori-mobile-menu ${isMobileMenuOpen ? 'is-open' : ''}`}>
        <div className="tori-mobile-menu__overlay" onClick={() => setIsMobileMenuOpen(false)} />
        <div className="tori-mobile-menu__content">
          <div className="tori-mobile-menu__header">
            <span className="tori-mobile-menu__title">Menu</span>
            <button onClick={() => setIsMobileMenuOpen(false)} aria-label="Close menu">
              <X size={24} strokeWidth={1.5} />
            </button>
          </div>
          <nav className="tori-mobile-menu__nav">
            <Link to="/collections">Collections</Link>
            <Link to="/new-arrivals">New Arrivals</Link>
            <Link to="/bridal">Bridal & Engagement</Link>
            <Link to="/jewelry">High Jewelry</Link>
            <Link to="/gifts">Gifts</Link>
            <Link to="/about">About</Link>
            <Link to="/contact">Contact</Link>
          </nav>
        </div>
      </div>
    </>
  );
};
