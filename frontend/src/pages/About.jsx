import { motion } from 'framer-motion';
import { Shield, Gem, Award, History } from 'lucide-react';
import './About.css';

export const About = () => {
  return (
    <div className="about-page page-enter-active">
      {/* Hero Section */}
      <section className="about-hero">
        <div className="about-hero__img-wrap">
          <img src="https://images.unsplash.com/photo-1584302174850-83ef87342672?q=80&w=2000" alt="Jewelry Crafting" className="about-hero__img" />
          <div className="about-hero__overlay" />
        </div>
        <div className="container about-hero__content">
          <motion.span 
            className="about-hero__subtitle"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
          >
            Since 1924
          </motion.span>
          <motion.h1 
            className="about-hero__title"
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
          >
            A Century of<br/>Master Craftsmanship
          </motion.h1>
        </div>
      </section>

      {/* Story Sections */}
      <section className="section about-story">
        <div className="container">
          <div className="about-grid">
            <motion.div 
              className="about-grid__text"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
            >
              <h2 className="about-title">The Heritage</h2>
              <div className="gold-divider" />
              <p>
                The story of Tori Crown began in a small atelier, where our founder envisioned a brand that would redefine luxury through the lens of timeless elegance. What started as a passion for rare gemstones evolved into a dynasty of master jewelers.
              </p>
              <p>
                Every piece we create is a testament to this hundred-year journey. We don't just make jewelry; we preserve moments, celebrate milestones, and craft legacies that are passed down through generations.
              </p>
            </motion.div>
            <motion.div 
              className="about-grid__img-wrap"
              initial={{ opacity: 0, scale: 0.95 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
            >
              <img src="https://images.unsplash.com/photo-1601121141461-9d6647bca1ed?q=80&w=1200" alt="Vintage Jewelry Design" />
            </motion.div>
          </div>
        </div>
      </section>

      {/* Pillars / Values */}
      <section className="section bg-warm">
        <div className="container">
          <div className="about-pillars">
            {[
              { icon: History, title: "Heritage", desc: "A century of tradition in every diamond cut and gold setting." },
              { icon: Gem, title: "Purity", desc: "Only the finest 22K and 21K gold, ethically sourced and certified." },
              { icon: Shield, title: "Trust", desc: "Lifetime authenticity certificates for every piece in our collection." },
              { icon: Award, title: "Excellence", desc: "Award-winning designs that blend modern art with classical soul." }
            ].map((pillar, idx) => (
              <motion.div 
                key={idx} 
                className="pillar-card"
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.1 }}
              >
                <div className="pillar-card__icon">
                  <pillar.icon size={32} strokeWidth={1} />
                </div>
                <h3>{pillar.title}</h3>
                <p>{pillar.desc}</p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Craftsmanship Focus */}
      <section className="section about-craft">
        <div className="container">
          <div className="about-grid about-grid--reverse">
            <motion.div 
              className="about-grid__text"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
            >
              <h2 className="about-title">The Art of Detail</h2>
              <div className="gold-divider" />
              <p>
                Our craftsmen spend hundreds of hours on a single high-jewelry piece. From the initial hand-drawn sketch to the final polish, every step is performed with surgical precision.
              </p>
              <p>
                We utilize the legendary 'Open Setting' technique for our diamonds, allowing light to enter from all angles and unleashing a brilliance that is uniquely Tori Crown.
              </p>
            </motion.div>
            <motion.div 
              className="about-grid__img-wrap"
              initial={{ opacity: 0, scale: 0.95 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
            >
              <img src="https://images.unsplash.com/photo-1573408301145-b98c4af01158?q=80&w=1200" alt="Master Craftsman" />
            </motion.div>
          </div>
        </div>
      </section>

      {/* Quote Section */}
      <section className="about-quote">
        <div className="container">
          <motion.blockquote
            initial={{ opacity: 0 }}
            whileInView={{ opacity: 1 }}
            viewport={{ once: true }}
          >
            "Jewelry is not just an accessory; it is a whisper of the soul made visible through gold and stone."
            <footer className="about-quote__footer">— The Tori Crown Ethos</footer>
          </motion.blockquote>
        </div>
      </section>
    </div>
  );
};
