import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useQuery } from '@tanstack/react-query';
import { useSearchParams } from 'react-router-dom';
import { getProducts } from '../services/api';
import { ProductCard } from '../ui/ProductCard';
import { Loader } from '../ui/Loader';
import { X, SlidersHorizontal } from 'lucide-react';
import './Collections.css';

const CATEGORIES = ['All', 'Diamond', 'Gold', 'Bridal', 'Minimalist', 'Gemstone'];
const SORT_OPTIONS = [
  { label: 'Newest', value: 'newest' },
  { label: 'Price: Low to High', value: 'price-asc' },
  { label: 'Price: High to Low', value: 'price-desc' },
  { label: 'Rating', value: 'rating' },
];

export const Collections = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const activeCategory = searchParams.get('category') || 'All';
  const [activeSort, setActiveSort] = useState('newest');
  const [showMobileFilters, setShowMobileFilters] = useState(false);

  const { data: products = [], isLoading } = useQuery({
    queryKey: ['products', activeCategory, activeSort],
    queryFn: () => getProducts({ 
      category: activeCategory === 'All' ? null : activeCategory,
      sort: activeSort 
    })
  });

  const handleCategoryChange = (cat) => {
    setSearchParams({ category: cat });
    if (showMobileFilters) setShowMobileFilters(false);
  };

  if (isLoading) return <Loader />;

  return (
    <div className="collections-page page-enter-active">
      {/* Header Section */}
      <header className="collections-hero">
        <div className="container">
          <motion.h1 
            className="collections-hero__title"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            {activeCategory === 'All' ? 'The Maison Collections' : `${activeCategory} Collection`}
          </motion.h1>
          <div className="gold-divider-center" />
          <p className="collections-hero__desc">
            Discover our masterfully crafted pieces, where a century of heritage meets contemporary design.
          </p>
        </div>
      </header>

      <div className="container">
        <div className="collections-layout">
          {/* Sidebar Filters (Desktop) */}
          <aside className="collections-sidebar">
            <div className="filter-group">
              <h3 className="filter-group__title">Categories</h3>
              <ul className="filter-list">
                {CATEGORIES.map(cat => (
                  <li key={cat}>
                    <button 
                      className={`filter-btn ${activeCategory === cat ? 'is-active' : ''}`}
                      onClick={() => handleCategoryChange(cat)}
                    >
                      {cat}
                    </button>
                  </li>
                ))}
              </ul>
            </div>
            
            <div className="filter-group">
              <h3 className="filter-group__title">Sort By</h3>
              <ul className="filter-list">
                {SORT_OPTIONS.map(opt => (
                  <li key={opt.value}>
                    <button 
                      className={`filter-btn ${activeSort === opt.value ? 'is-active' : ''}`}
                      onClick={() => setActiveSort(opt.value)}
                    >
                      {opt.label}
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          </aside>

          {/* Main Grid */}
          <main className="collections-main">
            {/* Mobile Toolbar */}
            <div className="mobile-toolbar">
              <button 
                className="mobile-filter-trigger"
                onClick={() => setShowMobileFilters(true)}
              >
                <SlidersHorizontal size={18} />
                Filters
              </button>
              <div className="results-count">
                {products.length} {products.length === 1 ? 'Piece' : 'Pieces'}
              </div>
            </div>

            {products.length > 0 ? (
              <div className="products-grid">
                <AnimatePresence mode="popLayout">
                  {products.map((product, idx) => (
                    <motion.div
                      key={product.id}
                      layout
                      initial={{ opacity: 0, scale: 0.9 }}
                      animate={{ opacity: 1, scale: 1 }}
                      exit={{ opacity: 0, scale: 0.9 }}
                      transition={{ duration: 0.4, delay: idx * 0.05 }}
                    >
                      <ProductCard product={product} />
                    </motion.div>
                  ))}
                </AnimatePresence>
              </div>
            ) : (
              <div className="no-results">
                <p>No pieces found in this category.</p>
                <button className="text-btn" onClick={() => handleCategoryChange('All')}>Clear All Filters</button>
              </div>
            )}
          </main>
        </div>
      </div>

      {/* Mobile Filter Overlay */}
      <AnimatePresence>
        {showMobileFilters && (
          <div className="mobile-filter-overlay">
            <motion.div 
              className="mobile-filter-overlay__backdrop"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setShowMobileFilters(false)}
            />
            <motion.div 
              className="mobile-filter-sheet"
              initial={{ y: '100%' }}
              animate={{ y: 0 }}
              exit={{ y: '100%' }}
              transition={{ type: 'spring', damping: 25, stiffness: 200 }}
            >
              <div className="mobile-filter-sheet__header">
                <h3>Refine Search</h3>
                <button onClick={() => setShowMobileFilters(false)}><X /></button>
              </div>
              <div className="mobile-filter-sheet__content">
                <div className="mobile-filter-section">
                  <h4>Category</h4>
                  <div className="chip-grid">
                    {CATEGORIES.map(cat => (
                      <button 
                        key={cat}
                        className={`chip ${activeCategory === cat ? 'is-active' : ''}`}
                        onClick={() => handleCategoryChange(cat)}
                      >
                        {cat}
                      </button>
                    ))}
                  </div>
                </div>
                <div className="mobile-filter-section">
                  <h4>Sort By</h4>
                  <div className="chip-grid">
                    {SORT_OPTIONS.map(opt => (
                      <button 
                        key={opt.value}
                        className={`chip ${activeSort === opt.value ? 'is-active' : ''}`}
                        onClick={() => {
                          setActiveSort(opt.value);
                          setShowMobileFilters(false);
                        }}
                      >
                        {opt.label}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};
