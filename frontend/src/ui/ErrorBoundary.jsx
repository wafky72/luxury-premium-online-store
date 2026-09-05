import React from 'react';
import { AlertCircle, RefreshCcw, Home } from 'lucide-react';
import './ErrorBoundary.css';

class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null, errorInfo: null };
  }

  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }

  componentDidCatch(error, errorInfo) {
    this.setState({ errorInfo });
    console.error("Uncaught error:", error, errorInfo);
  }

  componentDidMount() {
    this.errorHandler = (event) => {
      this.setState({
        hasError: true,
        error: event.error || new Error(event.message || 'Unknown global error')
      });
      console.error('Global Error Caught:', event.error);
    };

    this.promiseRejectionHandler = (event) => {
      // Don't show error screen for canceled requests or specific harmless API errors
      if (event.reason?.name === 'CanceledError') return;
      
      this.setState({
        hasError: true,
        error: event.reason instanceof Error ? event.reason : new Error(typeof event.reason === 'string' ? event.reason : 'Unhandled Promise Rejection')
      });
      console.error('Unhandled Promise Rejection Caught:', event.reason);
    };

    window.addEventListener('error', this.errorHandler);
    window.addEventListener('unhandledrejection', this.promiseRejectionHandler);
  }

  componentWillUnmount() {
    window.removeEventListener('error', this.errorHandler);
    window.removeEventListener('unhandledrejection', this.promiseRejectionHandler);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="error-boundary">
          <div className="error-boundary-content">
            <div className="error-icon-container">
              <AlertCircle className="error-icon" size={64} strokeWidth={1.5} />
            </div>
            
            <h1 className="error-heading">Something unexpected happened</h1>
            
            <p className="error-description">
              We apologize for the interruption. An unexpected error has occurred while trying to render this page.
            </p>
            
            {import.meta.env.DEV && this.state.error && (
              <div className="error-details">
                <p className="error-message-text">{this.state.error.toString()}</p>
                <details>
                  <summary>View Stack Trace</summary>
                  <pre>{this.state.errorInfo?.componentStack}</pre>
                </details>
              </div>
            )}
            
            <div className="error-actions">
              <button 
                className="gold-btn btn-refresh"
                onClick={() => window.location.reload()}
              >
                <RefreshCcw size={18} />
                <span>Refresh Page</span>
              </button>
              
              <button 
                className="gold-btn-outline btn-home"
                onClick={() => window.location.href = '/'}
              >
                <Home size={18} />
                <span>Return Home</span>
              </button>
            </div>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
