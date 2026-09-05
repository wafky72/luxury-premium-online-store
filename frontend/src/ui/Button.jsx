import { forwardRef } from 'react';
import { Link } from 'react-router-dom';
import './Button.css';

export const Button = forwardRef(({ 
  children, 
  variant = 'primary', 
  size = 'md', 
  fullWidth = false, 
  isLoading = false,
  className = '',
  as: Component,
  to,
  href,
  ...props 
}, ref) => {
  const classes = [
    'tori-btn',
    `tori-btn--${variant}`,
    `tori-btn--${size}`,
    fullWidth ? 'tori-btn--full' : '',
    isLoading ? 'tori-btn--loading' : '',
    className
  ].filter(Boolean).join(' ');

  let Element = Component || 'button';
  if (!Component) {
    if (to) Element = Link;
    else if (href) Element = 'a';
  }

  const conditionalProps = {};
  if (to) conditionalProps.to = to;
  if (href) conditionalProps.href = href;

  return (
    <Element ref={ref} className={classes} disabled={isLoading && Element === 'button'} {...conditionalProps} {...props}>
      <span className="tori-btn__content">
        {children}
      </span>
      {isLoading && (
        <span className="tori-btn__loader" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v2m0 12v2m8-8h-2M6 12H4m15.364 6.364l-1.414-1.414M7.05 7.05L5.636 5.636m12.728 0l-1.414 1.414M7.05 16.95l-1.414 1.414" />
          </svg>
        </span>
      )}
    </Element>
  );
});

Button.displayName = 'Button';
