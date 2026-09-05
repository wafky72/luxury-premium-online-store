import { Link } from 'react-router-dom';
import './Footer.css';

const IconInstagram = ({ size = 20 }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
  </svg>
);

const IconFacebook = ({ size = 20 }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.81l.19-4h-4V7a1 1 0 0 1 1-1h3z"></path>
  </svg>
);

const IconTwitter = ({ size = 20 }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
  </svg>
);

const IconYoutube = ({ size = 20 }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"></path>
    <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
  </svg>
);

export const Footer = () => {
  return (
    <footer className="tori-footer">
      <div className="container">
        <div className="tori-footer__grid">
          
          <div className="tori-footer__brand">
            <div className="tori-footer__logo-container">
              <img src="/logo.png" alt="" className="tori-footer__logo-img" />
              <span className="tori-footer__logo-text">Tori Crown</span>
            </div>
            <p className="tori-footer__desc">
              Exquisite handcrafted jewelry, born from a century-old tradition of master craftsmanship. 
              Designed for eternity.
            </p>
            <div className="tori-footer__social">
              <a href="#" aria-label="Instagram" className="social-icon-btn"><IconInstagram size={20} /></a>
              <a href="#" aria-label="Facebook" className="social-icon-btn"><IconFacebook size={20} /></a>
              <a href="#" aria-label="Twitter" className="social-icon-btn"><IconTwitter size={20} /></a>
              <a href="#" aria-label="Youtube" className="social-icon-btn"><IconYoutube size={20} /></a>
            </div>
          </div>

          <div className="tori-footer__links">
            <h3 className="tori-footer__heading">Shop</h3>
            <ul>
              <li><Link to="/collections">All Collections</Link></li>
              <li><Link to="/jewelry">High Jewelry</Link></li>
              <li><Link to="/bridal">Bridal & Engagement</Link></li>
              <li><Link to="/gifts">Gifts</Link></li>
              <li><Link to="/new">New Arrivals</Link></li>
            </ul>
          </div>

          <div className="tori-footer__links">
            <h3 className="tori-footer__heading">About</h3>
            <ul>
              <li><Link to="/about">Our story</Link></li>
              <li><Link to="/craftsmanship">Craftsmanship</Link></li>
              <li><Link to="/sustainability">Sustainability</Link></li>
              <li><Link to="/boutiques">Find a Boutique</Link></li>
              <li><Link to="/careers">Careers</Link></li>
            </ul>
          </div>

          <div className="tori-footer__links">
            <h3 className="tori-footer__heading">Client Care</h3>
            <ul>
              <li><Link to="/contact">Contact Us</Link></li>
              <li><Link to="/faq">FAQ</Link></li>
              <li><Link to="/shipping">Shipping & Returns</Link></li>
              <li><Link to="/care">Jewelry Care</Link></li>
              <li><Link to="/appointment">Book an Appointment</Link></li>
            </ul>
          </div>

        </div>

        <div className="tori-footer__bottom">
          <p>&copy; {new Date().getFullYear()} Tori Crown. All Rights Reserved.</p>
          <div className="tori-footer__legal">
            <Link to="/privacy">Privacy Policy</Link>
            <Link to="/terms">Terms of Service</Link>
          </div>
        </div>
      </div>
    </footer>
  );
};
