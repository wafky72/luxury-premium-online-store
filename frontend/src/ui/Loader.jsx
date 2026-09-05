import { motion } from 'framer-motion';
import './Loader.css';

export const Loader = ({ fullScreen = true }) => {
  return (
    <div className={`tori-unique-loader ${fullScreen ? 'tori-unique-loader--full' : ''}`}>
      <div className="tori-unique-loader__bg-panels">
        <motion.div 
          className="tori-unique-loader__panel tori-unique-loader__panel--left"
          initial={{ x: 0 }}
          exit={{ x: '-100%' }}
          transition={{ duration: 1.2, ease: [0.77, 0, 0.175, 1] }}
        />
        <motion.div 
          className="tori-unique-loader__panel tori-unique-loader__panel--right"
          initial={{ x: 0 }}
          exit={{ x: '100%' }}
          transition={{ duration: 1.2, ease: [0.77, 0, 0.175, 1] }}
        />
      </div>

      <div className="tori-unique-loader__content">
        <motion.div 
          className="tori-unique-loader__logo-wrap"
          initial={{ opacity: 0, scale: 0.8 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 1.5, ease: "easeOut" }}
        >
          {/* The Portal Ring */}
          <motion.div 
            className="tori-unique-loader__portal"
            animate={{ rotate: 360 }}
            transition={{ duration: 8, repeat: Infinity, ease: "linear" }}
          />

          <div className="tori-unique-loader__brand-mark">
            <img src="/logo.png" alt="Tori Crown" className="tori-unique-loader__img" />
            <div className="tori-unique-loader__shimmer-sweep" />
          </div>

          <motion.div 
            className="tori-unique-loader__text-wrap"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: 0.5, duration: 1 }}
          >
            <span className="tori-unique-loader__name">Tori Crown</span>
            <span className="tori-unique-loader__tagline">Crafted for Eternity</span>
          </motion.div>
        </motion.div>

        {/* The Minimalist Counter */}
        <div className="tori-unique-loader__counter">
          <motion.div 
            className="tori-unique-loader__progress"
            initial={{ width: 0 }}
            animate={{ width: '100%' }}
            transition={{ duration: 3, ease: "easeInOut" }}
          />
        </div>
      </div>
    </div>
  );
};
