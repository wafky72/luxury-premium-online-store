import React, { useRef, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { CheckCircle, Download, X, Package, Truck, Receipt } from 'lucide-react';

export const OrderSuccessModal = ({ order, onClose }) => {
  const invoiceRef = useRef(null);
  const [isDownloading, setIsDownloading] = useState(false);

  const downloadInvoice = async () => {
    setIsDownloading(true);
    try {
      const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
      const baseUrl = new URL(apiUrl).origin;
      window.open(`${baseUrl}/admin/orders/${order.id}/invoice-download`, '_blank');
    } catch (error) {
      console.error('Invoice generation failed', error);
      alert('Failed to download invoice. Please try again.');
    } finally {
      setTimeout(() => setIsDownloading(false), 1500);
    }
  };

  if (!order) return null;

  const totalFormat = (val) => `BDT ${Number(val).toLocaleString('en-US')}`;

  return (
    <>
      {/* BEAUTIFUL TOAST */}
      <AnimatePresence>
        <motion.div
          initial={{ opacity: 0, y: -50, x: '50%' }}
          animate={{ opacity: 1, y: 20, x: '50%' }}
          exit={{ opacity: 0, y: -50, x: '50%' }}
          transition={{ duration: 0.5, type: 'spring' }}
          style={{
            position: 'fixed',
            top: 0,
            right: '50%',
            zIndex: 99999,
            background: 'linear-gradient(135deg, #0d1117 0%, #161b22 100%)',
            color: '#fff',
            padding: '16px 24px',
            borderRadius: '50px',
            display: 'flex',
            alignItems: 'center',
            gap: '12px',
            boxShadow: '0 10px 40px rgba(0,0,0,0.3)',
            border: '1px solid rgba(255, 255, 255, 0.1)'
          }}
        >
          <div style={{
            background: '#10b981',
            borderRadius: '50%',
            padding: '4px',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
          }}>
            <CheckCircle size={18} color="#000" strokeWidth={3} />
          </div>
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <span style={{ fontWeight: 600, fontSize: '14px' }}>Order Successful!</span>
            <span style={{ fontSize: '12px', color: '#a1a1aa' }}>Your order {order.order_number} has been placed.</span>
          </div>
        </motion.div>
      </AnimatePresence>

      {/* POPUP MODAL */}
      <AnimatePresence>
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          style={{
            position: 'fixed',
            inset: 0,
            background: 'rgba(0, 0, 0, 0.8)',
            backdropFilter: 'blur(8px)',
            zIndex: 9999,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            padding: '20px'
          }}
        >
          <motion.div
            initial={{ scale: 0.9, opacity: 0, y: 30 }}
            animate={{ scale: 1, opacity: 1, y: 0 }}
            exit={{ scale: 0.9, opacity: 0, y: 30 }}
            transition={{ type: 'spring', damping: 25, stiffness: 300, delay: 0.2 }}
            style={{
              background: '#fff',
              borderRadius: '24px',
              width: '100%',
              maxWidth: '480px',
              overflow: 'hidden',
              boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
              position: 'relative'
            }}
          >
            {/* Modal Header */}
            <div style={{
              background: 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
              padding: '40px 24px 30px',
              textAlign: 'center',
              borderBottom: '1px solid #dee2e6'
            }}>
              <button 
                onClick={onClose}
                style={{
                  position: 'absolute',
                  top: '20px',
                  right: '20px',
                  background: '#fff',
                  border: 'none',
                  borderRadius: '50%',
                  width: '36px',
                  height: '36px',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  cursor: 'pointer',
                  boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
                }}
              >
                <X size={18} color="#495057" />
              </button>
              
              <motion.div 
                initial={{ scale: 0 }}
                animate={{ scale: 1 }}
                transition={{ type: 'spring', delay: 0.4 }}
                style={{
                  width: '80px',
                  height: '80px',
                  background: '#10b981',
                  borderRadius: '50%',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  margin: '0 auto 20px',
                  boxShadow: '0 10px 25px rgba(16, 185, 129, 0.4)'
                }}
              >
                <CheckCircle size={40} color="#fff" />
              </motion.div>
              <h2 style={{ margin: '0 0 8px', fontSize: '24px', fontWeight: 700, color: '#212529' }}>Thank You, {order.recipient_name.split(' ')[0]}!</h2>
              <p style={{ margin: 0, color: '#6c757d', fontSize: '15px' }}>Your luxury pieces are on their way.</p>
            </div>

            {/* Modal Body */}
            <div style={{ padding: '30px 24px' }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '24px' }}>
                <div>
                  <div style={{ fontSize: '12px', color: '#6c757d', textTransform: 'uppercase', letterSpacing: '0.5px' }}>Order Number</div>
                  <div style={{ fontSize: '16px', fontWeight: 600, color: '#212529', marginTop: '4px' }}>{order.order_number}</div>
                </div>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ fontSize: '12px', color: '#6c757d', textTransform: 'uppercase', letterSpacing: '0.5px' }}>Total Amount</div>
                  <div style={{ fontSize: '16px', fontWeight: 600, color: '#212529', marginTop: '4px' }}>{totalFormat(order.total)}</div>
                </div>
              </div>

              <div style={{ 
                background: '#f8f9fa', 
                borderRadius: '16px', 
                padding: '16px',
                display: 'flex',
                flexDirection: 'column',
                gap: '12px',
                marginBottom: '30px'
              }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                  <div style={{ background: '#e9ecef', padding: '8px', borderRadius: '8px' }}><Package size={16} color="#495057" /></div>
                  <div style={{ fontSize: '14px', color: '#495057' }}>
                    <span style={{ fontWeight: 600, color: '#212529' }}>{order.items?.length || 0} items</span> to be shipped
                  </div>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                  <div style={{ background: '#e9ecef', padding: '8px', borderRadius: '8px' }}><Truck size={16} color="#495057" /></div>
                  <div style={{ fontSize: '14px', color: '#495057' }}>
                    Delivery to <span style={{ fontWeight: 600, color: '#212529' }}>{order.shipping_city}, {order.shipping_district}</span>
                  </div>
                </div>
              </div>

              <button
                onClick={downloadInvoice}
                disabled={isDownloading}
                style={{
                  width: '100%',
                  background: '#0d1117',
                  color: '#fff',
                  border: 'none',
                  borderRadius: '12px',
                  padding: '16px',
                  fontSize: '16px',
                  fontWeight: 600,
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  gap: '10px',
                  cursor: isDownloading ? 'not-allowed' : 'pointer',
                  transition: 'background 0.2s',
                  boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
                }}
              >
                {isDownloading ? (
                  <span style={{ opacity: 0.8 }}>Generating PDF...</span>
                ) : (
                  <>
                    <Download size={20} />
                    Download Order Invoice
                  </>
                )}
              </button>
              
              <div style={{ textAlign: 'center', marginTop: '16px' }}>
                <button 
                  onClick={onClose}
                  style={{
                    background: 'none',
                    border: 'none',
                    color: '#6c757d',
                    fontSize: '14px',
                    fontWeight: 500,
                    cursor: 'pointer',
                    textDecoration: 'underline'
                  }}
                >
                  Continue Shopping
                </button>
              </div>
            </div>
          </motion.div>
        </motion.div>
      </AnimatePresence>


    </>
  );
};

export default OrderSuccessModal;
