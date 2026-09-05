import { useState } from 'react';
import { motion } from 'framer-motion';
import { MapPin, Phone, Mail, Clock, Send, MessageCircle } from 'lucide-react';
import './Contact.css';

export const Contact = () => {
  const [formStatus, setFormStatus] = useState('idle'); // idle, sending, success

  const handleSubmit = (e) => {
    e.preventDefault();
    setFormStatus('sending');
    setTimeout(() => setFormStatus('success'), 2000);
  };

  return (
    <div className="contact-page page-enter-active">
      {/* Header */}
      <header className="contact-header">
        <div className="container">
          <motion.h1 
            className="contact-header__title"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            Contact the Maison
          </motion.h1>
          <div className="gold-divider-center" />
          <p className="contact-header__desc">
            Our concierge team is at your service to assist with product inquiries, bespoke orders, and boutique appointments.
          </p>
        </div>
      </header>

      <div className="container">
        <div className="contact-layout">
          {/* Contact Information */}
          <aside className="contact-info">
            <div className="contact-card">
              <h2 className="contact-card__title">Concierge Service</h2>
              <div className="contact-item">
                <Phone size={20} className="contact-item__icon" />
                <div>
                  <label>Call Us</label>
                  <p>+1 (800) TORI-CROWN</p>
                </div>
              </div>
              <div className="contact-item">
                <Mail size={20} className="contact-item__icon" />
                <div>
                  <label>Email Us</label>
                  <p>concierge@toricrown.com</p>
                </div>
              </div>
              <div className="contact-item">
                <MessageCircle size={20} className="contact-item__icon" />
                <div>
                  <label>WhatsApp</label>
                  <p>+1 (555) LUX-JEWEL</p>
                </div>
              </div>
            </div>

            <div className="contact-card">
              <h2 className="contact-card__title">Flagship Boutique</h2>
              <div className="contact-item">
                <MapPin size={20} className="contact-item__icon" />
                <div>
                  <label>Visit Us</label>
                  <p>725 Fifth Avenue<br/>New York, NY 10022</p>
                </div>
              </div>
              <div className="contact-item">
                <Clock size={20} className="contact-item__icon" />
                <div>
                  <label>Boutique Hours</label>
                  <p>Mon - Sat: 10:00 - 19:00<br/>Sun: 12:00 - 18:00</p>
                </div>
              </div>
            </div>
          </aside>

          {/* Contact Form */}
          <main className="contact-form-container">
            {formStatus === 'success' ? (
              <motion.div 
                className="contact-success"
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
              >
                <div className="contact-success__icon">
                  <Send size={48} />
                </div>
                <h2>Message Received</h2>
                <p>Thank you for reaching out. A client advisor will contact you within 24 hours.</p>
                <button className="gold-btn" onClick={() => setFormStatus('idle')}>Send Another Message</button>
              </motion.div>
            ) : (
              <form className="contact-form" onSubmit={handleSubmit}>
                <div className="form-row">
                  <div className="form-group">
                    <label htmlFor="name">Full Name</label>
                    <input type="text" id="name" required placeholder="Johnathan Doe" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="email">Email Address</label>
                    <input type="email" id="email" required placeholder="john@example.com" />
                  </div>
                </div>

                <div className="form-group">
                  <label htmlFor="subject">Inquiry Type</label>
                  <select id="subject" required>
                    <option value="">Select an option</option>
                    <option value="product">Product Inquiry</option>
                    <option value="appointment">Book an Appointment</option>
                    <option value="bespoke">Bespoke Design</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div className="form-group">
                  <label htmlFor="message">Your Message</label>
                  <textarea id="message" required rows="6" placeholder="How can we assist you today?"></textarea>
                </div>

                <button 
                  type="submit" 
                  className="contact-submit-btn" 
                  disabled={formStatus === 'sending'}
                >
                  {formStatus === 'sending' ? 'Sending...' : 'Send Message'}
                </button>
              </form>
            )}
          </main>
        </div>
      </div>
    </div>
  );
};
