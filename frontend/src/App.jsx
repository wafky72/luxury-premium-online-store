import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { MainLayout } from './layouts/MainLayout';
import { Home } from './pages/Home';
import { ProductDetails } from './pages/ProductDetails';
import { Checkout } from './pages/Checkout';
import { Profile } from './pages/Profile';
import { Auth } from './pages/Auth';
import { Collections } from './pages/Collections';
import { About } from './pages/About';
import { Contact } from './pages/Contact';
import { Wishlist } from './pages/Wishlist';
import { InfoPage } from './pages/InfoPage';
import { Loader } from './ui/Loader';

function App() {
  const [isInitialLoading, setIsInitialLoading] = useState(true);

  useEffect(() => {
    // Hide loader after animation completes (approx 3s)
    const timer = setTimeout(() => {
      setIsInitialLoading(false);
    }, 3000);
    return () => clearTimeout(timer);
  }, []);

  return (
    <BrowserRouter>
      <AnimatePresence mode="wait">
        {isInitialLoading && <Loader key="splash-loader" />}
      </AnimatePresence>
      <Routes>
        <Route path="/" element={<MainLayout />}>
          <Route index element={<Home />} />
          <Route path="product/:slug" element={<ProductDetails key={window.location.pathname} />} />
          <Route path="checkout" element={<Checkout />} />
          <Route path="profile" element={<Profile />} />
          <Route path="login" element={<Auth />} />
          <Route path="register" element={<Auth />} />
          
          {/* Shop Routes */}
          <Route path="collections" element={<Collections />} />
          <Route path="new" element={<Collections />} />
          <Route path="new-arrivals" element={<Collections />} />
          <Route path="bridal" element={<Collections />} />
          <Route path="gifts" element={<Collections />} />
          <Route path="jewelry" element={<Collections />} />
          
          {/* Main Pages */}
          <Route path="about" element={<About />} />
          <Route path="contact" element={<Contact />} />
          <Route path="wishlist" element={<Wishlist />} />

          {/* Info Pages (Footer Links) */}
          <Route path="faq" element={<InfoPage />} />
          <Route path="shipping" element={<InfoPage />} />
          <Route path="care" element={<InfoPage />} />
          <Route path="privacy" element={<InfoPage />} />
          <Route path="terms" element={<InfoPage />} />
          <Route path="craftsmanship" element={<InfoPage />} />
          <Route path="sustainability" element={<InfoPage />} />
          <Route path="boutiques" element={<InfoPage />} />
          <Route path="careers" element={<InfoPage />} />
          <Route path="appointment" element={<InfoPage />} />
          
          <Route path="*" element={
            <div style={{padding: '20vh 0', textAlign: 'center', minHeight: '60vh'}}>
              <h2>Page Not Found</h2>
              <p>The page you are looking for does not exist.</p>
            </div>
          } />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
