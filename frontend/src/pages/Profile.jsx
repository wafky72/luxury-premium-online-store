import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { User, Package, Heart, MapPin, Settings, LogOut } from 'lucide-react';
import { useQuery } from '@tanstack/react-query';
import { Button } from '../ui/Button';
import { useWishlist } from '../store/useWishlist';
import { useAuth } from '../store/useAuth';
import { logout as apiLogout, getMyOrders } from '../services/api';
import { Loader } from '../ui/Loader';
import './Profile.css';

export const Profile = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const navigate = useNavigate();
  const { wishlistCount } = useWishlist();
  const { user, logout: localLogout, isAuthenticated } = useAuth();

  const { data: orders = [], isLoading: isLoadingOrders } = useQuery({
    queryKey: ['orders'],
    queryFn: getMyOrders,
    enabled: isAuthenticated
  });

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
    }
  }, [isAuthenticated, navigate]);

  if (!isAuthenticated) return null;

  const handleLogout = async () => {
    try {
      await apiLogout();
    } finally {
      localLogout();
      navigate('/');
    }
  };

  const renderContent = () => {
    if (isLoadingOrders) return <Loader />;

    switch (activeTab) {
      case 'dashboard':
        return (
          <div className="profile-dashboard">
            <h2 className="profile-section-title">Welcome back, {user?.name || 'Valued Client'}</h2>
            <div className="dashboard-grid">
              <div className="dashboard-card" onClick={() => setActiveTab('orders')} style={{cursor: 'pointer'}}>
                <Package size={32} className="dashboard-card__icon" strokeWidth={1.5} />
                <div className="dashboard-card__value">{orders.length}</div>
                <div className="dashboard-card__label">Total Orders</div>
              </div>
              <div className="dashboard-card" onClick={() => navigate('/wishlist')} style={{cursor: 'pointer'}}>
                <Heart size={32} className="dashboard-card__icon" strokeWidth={1.5} />
                <div className="dashboard-card__value">{wishlistCount}</div>
                <div className="dashboard-card__label">Wishlist Items</div>
              </div>
            </div>
            
            <div style={{marginTop: 'var(--space-3xl)'}}>
              <h3 className="profile-section-title" style={{fontSize: '1.2rem'}}>Recent Order</h3>
              {orders.length > 0 ? (
                orders.slice(0,1).map(order => (
                  <div key={order.id} className="order-card">
                    <div className="order-card__info">
                      <span className="order-card__id">{order.order_number}</span>
                      <span className="order-card__date">{new Date(order.created_at).toLocaleDateString()}</span>
                      <div>
                        <span className={`order-card__status status-${order.status.toLowerCase()}`}>
                          {order.status}
                        </span>
                      </div>
                    </div>
                    <div className="order-card__action">
                      <span className="order-card__total">${order.total.toLocaleString()}</span>
                      <Button variant="outline" size="sm" style={{marginLeft: '1rem'}}>View</Button>
                    </div>
                  </div>
                ))
              ) : (
                <p className="profile-empty-inline">No recent orders found.</p>
              )}
            </div>
          </div>
        );

      case 'orders':
        return (
          <div className="profile-orders">
            <h2 className="profile-section-title">Order History</h2>
            {orders.length > 0 ? (
              <div className="order-list">
                {orders.map(order => (
                  <div key={order.id} className="order-card">
                    <div className="order-card__info">
                      <span className="order-card__id">{order.order_number}</span>
                      <span className="order-card__date">Placed on {new Date(order.created_at).toLocaleDateString()}</span>
                      <div>
                        <span className={`order-card__status status-${order.status.toLowerCase()}`}>
                          {order.status}
                        </span>
                      </div>
                    </div>
                    <div className="order-card__action">
                      <span className="order-card__total">${order.total.toLocaleString()}</span>
                      <Button variant="outline" size="sm" style={{marginLeft: '1rem'}}>Details</Button>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="profile-empty">
                <Package size={48} strokeWidth={1} style={{marginBottom: '1rem', opacity: 0.5}} />
                <p>You haven't placed any orders yet.</p>
                <Button variant="primary" style={{marginTop: '1.5rem'}} onClick={() => navigate('/collections')}>Start Shopping</Button>
              </div>
            )}
          </div>
        );

      case 'addresses':
      case 'settings':
        return (
          <div className="profile-coming-soon">
            <h2 className="profile-section-title">
              {activeTab === 'addresses' ? 'Saved Addresses' : 'Account Settings'}
            </h2>
            <div className="profile-empty">
              <Settings size={48} strokeWidth={1} style={{marginBottom: '1rem', opacity: 0.5}} />
              <p>This feature is currently under development.</p>
              <p style={{fontSize: '0.9rem', marginTop: '0.5rem'}}>Please contact our concierge for account updates.</p>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="profile-page page-transition">
      <div className="profile-header">
        <div className="container">
          <h1 className="profile-title">My Account</h1>
          <p className="profile-subtitle">Manage your orders, preferences, and wishlist.</p>
        </div>
      </div>

      <div className="container">
        <div className="profile-layout">
          {/* Sidebar */}
          <aside className="profile-sidebar">
            <nav className="profile-nav">
              <button 
                className={`profile-nav__btn ${activeTab === 'dashboard' ? 'is-active' : ''}`}
                onClick={() => setActiveTab('dashboard')}
              >
                <User size={18} /> Dashboard
              </button>
              <button 
                className={`profile-nav__btn ${activeTab === 'orders' ? 'is-active' : ''}`}
                onClick={() => setActiveTab('orders')}
              >
                <Package size={18} /> My Orders
              </button>
              <button 
                className="profile-nav__btn"
                onClick={() => navigate('/wishlist')}
              >
                <Heart size={18} /> Wishlist
              </button>
              <button 
                className={`profile-nav__btn ${activeTab === 'addresses' ? 'is-active' : ''}`}
                onClick={() => setActiveTab('addresses')}
              >
                <MapPin size={18} /> Addresses
              </button>
              <button 
                className={`profile-nav__btn ${activeTab === 'settings' ? 'is-active' : ''}`}
                onClick={() => setActiveTab('settings')}
              >
                <Settings size={18} /> Settings
              </button>
              
              <button className="profile-nav__btn is-logout" onClick={handleLogout}>
                <LogOut size={18} /> Sign Out
              </button>
            </nav>
          </aside>

          {/* Content */}
          <main className="profile-content">
            {renderContent()}
          </main>
        </div>
      </div>
    </div>
  );
};
