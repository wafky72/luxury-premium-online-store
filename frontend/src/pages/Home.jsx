import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { Button } from '../ui/Button';
import { ProductCard } from '../ui/ProductCard';
import { useQuery } from '@tanstack/react-query';
import { getHeroData, getBestSellers, getstoryContent, getTrustBadges } from '../services/api';
import { Loader } from '../ui/Loader';
import { Shield, Gem, Truck, RefreshCcw } from 'lucide-react';
import './Home.css';

const IconMap = {
  shield: Shield,
  gem: Gem,
  truck: Truck,
  refresh: RefreshCcw,
};

export const Home = () => {
  const { data: heroData, isLoading: heroLoading } = useQuery({ queryKey: ['hero'], queryFn: getHeroData });
  const { data: bestSellers = [], isLoading: bestSellersLoading } = useQuery({ queryKey: ['best-sellers'], queryFn: getBestSellers });
  const { data: storyContent, isLoading: StoryLoading } = useQuery({ queryKey: ['Story'], queryFn: getstoryContent });
  const { data: trustBadges = [], isLoading: trustLoading } = useQuery({ queryKey: ['trust-badges'], queryFn: getTrustBadges });

  const isLoading = heroLoading || bestSellersLoading || StoryLoading || trustLoading;

  if (isLoading || !heroData) {
    return <Loader />;
  }

  return (
    <div className="page-enter-active">
      
      {/* Hero Section - Editorial Split Layout */}
      <section className="hero-editorial bg-dark">
        <div className="container hero-editorial__container">
          <div className="hero-editorial__content">
            <motion.span 
              className="hero-editorial__subtitle"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
            >
              The Maison Collection
            </motion.span>
            <motion.h1 
              className="hero-editorial__title"
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.4 }}
            >
              Crafted for<br/>Eternity
            </motion.h1>
            <motion.p
              className="hero-editorial__desc"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.6 }}
            >
              {heroData.subheadline}
            </motion.p>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.8 }}
            >
              <Button size="lg" variant="accent" onClick={() => document.getElementById('collections').scrollIntoView()}>
                {heroData?.cta || 'Explore Collection'}
              </Button>
            </motion.div>
          </div>

          <div className="hero-editorial__visuals">
            <motion.div 
              className="hero-editorial__img-main-wrapper"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.unsplash.com/photo-1573408301185-9146fe634ad0?auto=format&fit=crop&q=80&w=1200" alt="Tori Crown High Jewelry" className="img-main" onError={(e) => { e.target.src = 'https://via.placeholder.com/1000x1200/0A1128/C5A059?text=Tori+Crown'; }} />
            </motion.div>
            
            <motion.div 
              className="hero-editorial__img-sub-wrapper"
              initial={{ opacity: 0, x: -100, y: 100 }}
              animate={{ 
                opacity: 1, 
                x: 0, 
                y: [0, -10, 0] // Floating pulse effect
              }}
              transition={{ 
                opacity: { duration: 1.2, delay: 0.5 },
                x: { duration: 1.2, delay: 0.5, ease: "easeOut" },
                y: { 
                  duration: 4, 
                  repeat: Infinity, 
                  repeatType: "reverse", 
                  ease: "easeInOut" 
                } 
              }}
            >
              <img src="https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&q=80&w=600" alt="Jewelry Detail" className="img-sub" onError={(e) => { e.target.style.display = 'none'; }} />
            </motion.div>
          </div>
        </div>
      </section>

      {/* Trust Section */}
      <section className="trust-section">
        <div className="container">
          <div className="trust-grid">
            {trustBadges.map((badge, idx) => {
              const Icon = IconMap[badge.icon];
              return (
                <motion.div 
                  key={idx} 
                  className="trust-card"
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: idx * 0.1 }}
                >
                  <div className="trust-card__icon">
                    {Icon ? <Icon size={28} strokeWidth={1} /> : <Shield size={28} strokeWidth={1} />}
                  </div>
                  <h3 className="trust-card__title">{badge.title}</h3>
                  <p className="trust-card__desc">{badge.description}</p>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>
      {/* Best Sellers (Signature Pieces) */}
      <section className="section">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">Signature Pieces</h2>
            <div className="gold-divider-center" />
          </div>

          <div className="bestsellers-scroll-wrap">
            <div className="bestsellers-grid">
              {bestSellers.map(product => (
                <ProductCard key={`${product.id}-best`} product={product} />
              ))}
            </div>
          </div>
          
          <div className="bestsellers-actions">
            <Button variant="outline" as={Link} to="/collections">View All Jewelry</Button>
          </div>
        </div>
      </section>

      {/* Section Header for Collections */}
      <section id="collections" style={{ textAlign: 'center', paddingTop: 'var(--space-section)' }}>
        <div className="container">
          <h2 className="section-title">Discover the Collections</h2>
          <div className="gold-divider-center" />
        </div>
      </section>

      {/* Segment 1: High Jewelry / Diamonds (Reverse Layout) */}
      <section className="section" style={{ paddingTop: 'var(--space-2xl)' }}>
        <div className="container">
          <div className="promo-segment promo-segment--reverse">
            <motion.div 
              className="promo-banner"
              initial={{ opacity: 0, x: 50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.pexels.com/photos/2735970/pexels-photo-2735970.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Diamond Symphony" className="promo-banner__img" loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x800/0A1128/C5A059?text=Tori+Crown'; }} />
              <div className="promo-banner__overlay" />
              <div className="promo-banner__content">
                <span className="promo-banner__subtitle">High Jewelry</span>
                <h2 className="promo-banner__title">The Diamond Symphony</h2>
                <Button variant="primary" as={Link} to="/collections?category=Diamond">Shop Diamonds</Button>
              </div>
            </motion.div>
            <div className="promo-grid">
              {bestSellers.slice(0, 4).map(product => (
                <ProductCard key={`${product.id}-d`} product={product} />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Segment 2: Royal Kundan (Heritage) */}
      <section className="section bg-warm">
        <div className="container">
          <div className="promo-segment">
            <motion.div 
              className="promo-banner"
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.unsplash.com/photo-1512163143273-bde0e3cc7407?auto=format&fit=crop&q=80&w=1200" alt="Royal Kundan" className="promo-banner__img" style={{objectPosition: 'top'}} loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x800/0A1128/C5A059?text=Tori+Crown'; }} />
              <div className="promo-banner__overlay" />
              <div className="promo-banner__content">
                <span className="promo-banner__subtitle">Heritage Collection</span>
                <h2 className="promo-banner__title">Royal Kundan Series</h2>
                <Button variant="primary" as={Link} to="/collections?category=Gold">Discover Kundan</Button>
              </div>
            </motion.div>
            <div className="promo-grid">
              {bestSellers.slice(4, 8).map(product => (
                <ProductCard key={`${product.id}-k`} product={product} />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Segment 3: Everyday Minimalist (Reverse) */}
      <section className="section">
        <div className="container">
          <div className="promo-segment promo-segment--reverse">
            <motion.div 
              className="promo-banner"
              initial={{ opacity: 0, x: 50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.unsplash.com/photo-1573408301185-9146fe634ad0?auto=format&fit=crop&q=80&w=1200" alt="Minimalist Gold" className="promo-banner__img" loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x800/0A1128/C5A059?text=Tori+Crown'; }} />
              <div className="promo-banner__overlay" />
              <div className="promo-banner__content">
                <span className="promo-banner__subtitle">Daily Elegance</span>
                <h2 className="promo-banner__title">Minimalist 21K Gold</h2>
                <Button variant="primary" as={Link} to="/collections?category=Minimalist">Shop Everyday Wear</Button>
              </div>
            </motion.div>
            <div className="promo-grid">
              {bestSellers.slice(0, 4).reverse().map(product => (
                <ProductCard key={`${product.id}-m`} product={product} />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Segment 4: Eid Exclusive / Men & Women */}
      <section className="section bg-warm">
        <div className="container">
          <div className="promo-segment">
            <motion.div 
              className="promo-banner"
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&q=80&w=1200" alt="Eid Exclusive" className="promo-banner__img" loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x800/0A1128/C5A059?text=Tori+Crown'; }} />
              <div className="promo-banner__overlay" />
              <div className="promo-banner__content">
                <span className="promo-banner__subtitle">Festive Special</span>
                <h2 className="promo-banner__title">Eid Exclusive 2026</h2>
                <Button variant="primary" as={Link} to="/collections?category=All">View Festive Pieces</Button>
              </div>
            </motion.div>
            <div className="promo-grid">
              {bestSellers.slice(4, 8).reverse().map(product => (
                <ProductCard key={`${product.id}-e`} product={product} />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Promotional Segment: Left Banner + Right Grid */}
      <section className="section bg-warm">
        <div className="container">
          <div className="promo-segment">
            <motion.div 
              className="promo-banner"
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.2, ease: "easeOut" }}
            >
              <img src="https://images.unsplash.com/photo-1601121141461-9d6647bca1ed?auto=format&fit=crop&q=80&w=1200" alt="Bridal Collection" className="promo-banner__img" loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x800/0A1128/C5A059?text=Tori+Crown'; }} />
              <div className="promo-banner__overlay" />
              <div className="promo-banner__content">
                <span className="promo-banner__subtitle">The 2026 Edition</span>
                <h2 className="promo-banner__title">Bridal Masterpieces</h2>
                <Button variant="primary" as={Link} to="/collections?category=Bridal">Explore Collection</Button>
              </div>
            </motion.div>
            <div className="promo-grid">
              {bestSellers.slice(0, 4).map(product => (
                <ProductCard key={`${product.id}-bridal`} product={product} />
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Journal / Blog Section */}
      <section className="section">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">The Maison Journal</h2>
            <div className="gold-divider-center" />
            <p style={{textAlign: 'center', marginTop: '16px', color: 'var(--color-text-light)'}}>stories of craftsmanship, heritage, and style.</p>
          </div>
          
          <div className="journal-grid">
            {[
              {
                img: "https://images.unsplash.com/photo-1611085583191-a3b181a88401?q=80&w=800",
                cat: "Guide",
                title: "Understanding Gold Purity: 18K vs 22K vs 24K",
                date: "May 2, 2026"
              },
              {
                img: "https://images.pexels.com/photos/177332/pexels-photo-177332.jpeg?auto=compress&cs=tinysrgb&w=800",
                cat: "Craftsmanship",
                title: "The Art of the Perfect Cut: Behind Our Diamonds",
                date: "April 28, 2026"
              },
              {
                img: "https://images.unsplash.com/photo-1596944924616-7b38e7cfac36?q=80&w=800",
                cat: "Style",
                title: "How to Layer Necklaces Like a Professional Stylist",
                date: "April 15, 2026"
              }
            ].map((post, idx) => (
              <motion.article 
                key={idx}
                className="journal-card"
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: "-50px" }}
                transition={{ duration: 1.0, delay: idx * 0.15, ease: "easeOut" }}
              >
                <div className="journal-card__img-wrap">
                  <img src={post.img} alt={post.title} loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/800x600/0A1128/C5A059?text=Tori+Crown'; }} />
                </div>
                <div className="journal-card__content">
                  <span className="journal-card__category">{post.cat}</span>
                  <h3 className="journal-card__title">{post.title}</h3>
                  <span className="journal-card__date">{post.date}</span>
                </div>
              </motion.article>
            ))}
          </div>
        </div>
      </section>

      {/* Story Section */}
      <section className="story-section">
        <div className="story-image">
          <img src={storyContent.image} alt="Craftsmanship" loading="lazy" onError={(e) => { e.target.src = 'https://via.placeholder.com/1200x1200/0A1128/C5A059?text=Tori+Crown'; }} />
        </div>
        <div className="story-content">
          <motion.div 
            className="story-content__inner"
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.8 }}
          >
            <span className="story-subtitle">{storyContent.subtitle}</span>
            <h2 className="story-title">{storyContent.title}</h2>
            <div className="gold-divider" />
            <p className="story-body">{storyContent.body}</p>
            <p className="story-signature">{storyContent.signature}</p>
            <Button variant="outline" className="story-btn" as={Link} to="/about">Read Our Story</Button>
          </motion.div>
        </div>
      </section>

    </div>
  );
};
