import { motion } from 'framer-motion';
import { useLocation } from 'react-router-dom';
import './InfoPage.css';

const PAGE_CONTENT = {
  '/faq': {
    title: 'Frequently Asked Questions',
    subtitle: 'Client Care',
    content: (
      <>
        <h3>How can I track my order?</h3>
        <p>Once your piece has been dispatched, you will receive an email containing your tracking details and an estimated delivery date. Our concierge team is also available to assist you at any point.</p>
        
        <h3>Do you offer international shipping?</h3>
        <p>Yes, Tori Crown provides complimentary, fully-insured shipping globally for all high jewelry purchases.</p>

        <h3>What is your return policy?</h3>
        <p>We accept returns or exchanges within 30 days of delivery, provided the jewelry is in its original, unworn condition with all authenticity certificates included.</p>
      </>
    )
  },
  '/shipping': {
    title: 'Shipping & Returns',
    subtitle: 'Client Care',
    content: (
      <>
        <h3>Complimentary Insured Shipping</h3>
        <p>Every Tori Crown creation is delivered with complimentary express shipping. Each package is fully insured from our Maison to your door, ensuring complete peace of mind.</p>
        
        <h3>Signature Required</h3>
        <p>Due to the high value of our creations, an adult signature is required upon delivery. We cannot deliver to P.O. boxes.</p>
      </>
    )
  },
  '/care': {
    title: 'Jewelry Care',
    subtitle: 'Client Care',
    content: (
      <>
        <h3>Preserving Eternity</h3>
        <p>Your Tori Crown jewelry is crafted to last generations. To maintain its brilliance, we recommend storing your pieces individually in the provided velvet pouches to prevent scratching.</p>
        
        <h3>Cleaning Your Diamonds</h3>
        <p>For diamond and gold pieces, a gentle cleaning with warm water, mild soap, and a soft-bristled brush will restore their original sparkle. Avoid exposing gemstones to harsh chemicals or extreme temperatures.</p>
      </>
    )
  },
  '/privacy': {
    title: 'Privacy Policy',
    subtitle: 'Legal',
    content: (
      <>
        <h3>Your Privacy Matters</h3>
        <p>Tori Crown is committed to protecting your privacy. This policy outlines how we collect, use, and safeguard your personal information across our boutiques and digital platforms.</p>
        <p>We employ industry-leading encryption to ensure your payment details and personal data remain strictly confidential.</p>
      </>
    )
  },
  '/terms': {
    title: 'Terms of Service',
    subtitle: 'Legal',
    content: (
      <>
        <h3>Conditions of Use</h3>
        <p>By accessing the Tori Crown website, you agree to these terms of service. All content, imagery, and designs are the exclusive property of Tori Crown.</p>
        <p>We reserve the right to limit the sales of our products to any person or geographic region.</p>
      </>
    )
  },
  '/craftsmanship': {
    title: 'Craftsmanship',
    subtitle: 'Our Heritage',
    content: (
      <>
        <h3>The Hands of Masters</h3>
        <p>Every piece of Tori Crown jewelry is the result of hundreds of hours of meticulous labor by our master artisans. From the initial sketch to the final polish, perfection is our only standard.</p>
      </>
    )
  },
  '/sustainability': {
    title: 'Sustainability',
    subtitle: 'Our Heritage',
    content: (
      <>
        <h3>Ethical Sourcing</h3>
        <p>Tori Crown is dedicated to ethical practices. 100% of our diamonds are certified conflict-free in accordance with the Kimberley Process.</p>
        <p>We also utilize recycled 18K and 21K gold wherever possible without compromising the structural integrity or beauty of our designs.</p>
      </>
    )
  },
  '/boutiques': {
    title: 'Find a Boutique',
    subtitle: 'About',
    content: (
      <>
        <h3>Global Presence</h3>
        <p>Experience Tori Crown in person at our flagship boutiques. Our expert client advisors are available to guide you through our collections in a private, luxurious setting.</p>
        <p>Flagship Location: Gulshan Avenue, Dhaka.<br/>Coming soon: Dubai, London, New York.</p>
      </>
    )
  },
  '/careers': {
    title: 'Careers',
    subtitle: 'About',
    content: (
      <>
        <h3>Join the Maison</h3>
        <p>Tori Crown is always seeking exceptional talent in jewelry design, master crafting, and luxury retail.</p>
        <p>To inquire about current opportunities, please forward your portfolio and resume to careers@toricrown.com.</p>
      </>
    )
  },
  '/appointment': {
    title: 'Book an Appointment',
    subtitle: 'Client Care',
    content: (
      <>
        <h3>Private Consultations</h3>
        <p>Book a private consultation with a Tori Crown advisor. Whether you are selecting an engagement ring, a bespoke creation, or simply wish to view our high jewelry collection, we are at your service.</p>
        <p>Please contact our concierge via the Contact page to schedule your visit.</p>
      </>
    )
  }
};

export const InfoPage = () => {
  const location = useLocation();
  const pageData = PAGE_CONTENT[location.pathname] || {
    title: 'Information',
    subtitle: 'Tori Crown',
    content: <p>Information for this section will be updated shortly.</p>
  };

  return (
    <div className="info-page page-enter-active">
      <div className="container info-page__container">
        <motion.div 
          className="info-page__header"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <span className="info-page__subtitle">{pageData.subtitle}</span>
          <h1 className="info-page__title">{pageData.title}</h1>
          <div className="gold-divider-center" />
        </motion.div>

        <motion.div 
          className="info-page__content"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
        >
          {pageData.content}
        </motion.div>
      </div>
    </div>
  );
};
