import { Outlet, useLocation } from 'react-router-dom';
import { useEffect } from 'react';
import { Hammer } from 'lucide-react';
import { Header } from './Header';
import { Footer } from './Footer';
import { BottomNav } from './BottomNav';
import { CartDrawer } from './CartDrawer';
import { useCart } from '../store/useCart';
import './MainLayout.css';

const MaintenanceBanner = () => {
  return (
    <div className="tori-maintenance-banner">
      <div className="container tori-maintenance-banner__inner">
        <Hammer size={14} className="tori-maintenance-banner__icon pulse" strokeWidth={2.5} />
        <span className="tori-maintenance-banner__text">
          Maintenance Mode On <span className="separator">•</span> Development in Progress
        </span>
        <Hammer size={14} className="tori-maintenance-banner__icon pulse" strokeWidth={2.5} />
      </div>
    </div>
  );
};

export const MainLayout = () => {
  const { pathname } = useLocation();
  const isHome = pathname === '/';
  const { isCartOpen, closeCart } = useCart();

  // Scroll to top on route change
  useEffect(() => {
    window.scrollTo(0, 0);
  }, [pathname]);

  return (
    <div className={`tori-layout ${isHome ? 'has-maintenance' : ''}`}>
      {isHome && <MaintenanceBanner />}
      <Header />
      <main className="tori-layout__main">
        <Outlet />
      </main>
      <Footer />
      <BottomNav />
      
      <CartDrawer isOpen={isCartOpen} onClose={closeCart} />
    </div>
  );
};
