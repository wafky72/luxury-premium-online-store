import './Badge.css';

export const Badge = ({ children, variant = 'dark', className = '' }) => {
  if (!children) return null;
  return (
    <span className={`tori-badge tori-badge--${variant} ${className}`}>
      {children}
    </span>
  );
};
