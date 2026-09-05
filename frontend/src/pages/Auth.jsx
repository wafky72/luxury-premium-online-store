import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Button } from '../ui/Button';
import { login, register } from '../services/api';
import { useAuth } from '../store/useAuth';
import './Auth.css';

export const Auth = () => {
  const [mode, setMode] = useState('login'); // 'login' or 'register'
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: ''
  });

  const navigate = useNavigate();
  const { setAuth, isAuthenticated } = useAuth();

  useEffect(() => {
    if (isAuthenticated) {
      navigate('/profile');
    }
  }, [isAuthenticated, navigate]);

  const handleChange = (e) => {
    const { id, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [id]: value
    }));
  };

  const handleAuth = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError(null);

    try {
      let response;
      if (mode === 'login') {
        response = await login({
          email: formData.email,
          password: formData.password
        });
      } else {
        response = await register({
          name: formData.name,
          email: formData.email,
          password: formData.password,
          password_confirmation: formData.password_confirmation,
          phone: formData.phone
        });
      }

      if (response.success) {
        setAuth(response.data.user, response.data.token);
        navigate('/profile');
      }
    } catch (err) {
      setError(err.message || 'Authentication failed. Please check your credentials.');
      if (err.errors) {
        // Handle Laravel validation errors
        const firstError = Object.values(err.errors)[0][0];
        setError(firstError);
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleSocialAuth = (provider) => {
    // Social auth is still simulated or would redirect to backend
    console.log(`Starting social auth with ${provider}`);
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      navigate('/profile');
    }, 1000);
  };

  return (
    <div className="auth-page page-transition">
      <div className="auth-container">
        
        <div className="auth-header">
          <h1 className="auth-title">Welcome</h1>
          <p className="auth-subtitle">
            {mode === 'login' ? 'Sign in to access your account' : 'Create an account for exclusive access'}
          </p>
        </div>

        <div className="auth-tabs">
          <button 
            className={`auth-tab ${mode === 'login' ? 'is-active' : ''}`}
            onClick={() => {
              setMode('login');
              setError(null);
            }}
          >
            Sign In
          </button>
          <button 
            className={`auth-tab ${mode === 'register' ? 'is-active' : ''}`}
            onClick={() => {
              setMode('register');
              setError(null);
            }}
          >
            Create Account
          </button>
        </div>

        {error && <div className="auth-error">{error}</div>}

        <form className="auth-form" onSubmit={handleAuth}>
          {mode === 'register' && (
            <>
              <div className="auth-form-group">
                <label htmlFor="name">Full Name</label>
                <input 
                  type="text" 
                  id="name" 
                  placeholder="Enter your full name" 
                  value={formData.name}
                  onChange={handleChange}
                  required 
                />
              </div>
              <div className="auth-form-group">
                <label htmlFor="phone">Phone Number</label>
                <input 
                  type="tel" 
                  id="phone" 
                  placeholder="Enter your phone number" 
                  value={formData.phone}
                  onChange={handleChange}
                />
              </div>
            </>
          )}
          
          <div className="auth-form-group">
            <label htmlFor="email">Email Address</label>
            <input 
              type="email" 
              id="email" 
              placeholder="Enter your email" 
              value={formData.email}
              onChange={handleChange}
              required 
            />
          </div>
          
          <div className="auth-form-group">
            <label htmlFor="password">Password</label>
            <input 
              type="password" 
              id="password" 
              placeholder="Enter your password" 
              value={formData.password}
              onChange={handleChange}
              required 
            />
          </div>

          {mode === 'register' && (
            <div className="auth-form-group">
              <label htmlFor="password_confirmation">Confirm Password</label>
              <input 
                type="password" 
                id="password_confirmation" 
                placeholder="Confirm your password" 
                value={formData.password_confirmation}
                onChange={handleChange}
                required 
              />
            </div>
          )}

          {mode === 'login' && (
            <Link to="#" className="auth-forgot-password">Forgot password?</Link>
          )}

          <Button type="submit" variant="primary" fullWidth isLoading={isLoading} style={{marginTop: 'var(--space-sm)'}}>
            {mode === 'login' ? 'Sign In' : 'Create Account'}
          </Button>
        </form>

        <div className="auth-divider">Or continue with</div>

        <div className="auth-social">
          <button type="button" className="auth-social-btn" onClick={() => handleSocialAuth('google')} disabled={isLoading}>
            <svg className="auth-social-icon" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
              <path fill="none" d="M1 1h22v22H1z" />
            </svg>
            Continue with Google
          </button>
          
          <button type="button" className="auth-social-btn" onClick={() => handleSocialAuth('facebook')} disabled={isLoading}>
            <svg className="auth-social-icon" viewBox="0 0 24 24">
              <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
            </svg>
            Continue with Facebook
          </button>
        </div>
      </div>
    </div>
  );
};
