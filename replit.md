# Portfolio Website

## Overview

This is a personal portfolio website for Prasad Maddikar, a Computer Science Engineering Student and Full Stack Developer. The website is built as a static single-page application using modern web technologies with a focus on responsive design, smooth animations, and clean user experience.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Single Page Application (SPA)**: Built as a static website with smooth scrolling navigation between sections
- **Responsive Design**: Mobile-first approach using Bootstrap 5.3.2 framework
- **Component-based Structure**: Organized into distinct sections (Home, About, Experience, Projects, Skills, Contact)
- **Animation System**: Integrated AOS (Animate On Scroll) library for smooth entrance animations

### Technology Stack
- **HTML5**: Semantic markup with proper meta tags for SEO
- **CSS3**: Custom properties (CSS variables) for theming, Flexbox/Grid for layouts
- **Vanilla JavaScript**: For interactive features and scroll-based functionality
- **Bootstrap 5**: For responsive grid system and UI components
- **Font Awesome**: For iconography
- **Google Fonts**: Poppins font family for typography

## Key Components

### Navigation System
- Fixed-position navbar with brand name and navigation links
- Scroll-based styling changes (transparent to solid background)
- Active section highlighting based on scroll position
- Smooth scrolling implementation for anchor links
- Mobile-responsive hamburger menu

### Styling Architecture
- **CSS Custom Properties**: Centralized theming system with color variables
- **Dark Theme Support**: Theme switching capability with data attributes
- **Component-based CSS**: Modular styling approach
- **Animation System**: CSS transitions and AOS library integration

### JavaScript Functionality
- **Scroll Management**: Navbar state changes and active link highlighting
- **Smooth Navigation**: Enhanced anchor link behavior
- **Animation Initialization**: AOS library setup and configuration
- **Responsive Interactions**: Dynamic UI updates based on user actions

## Data Flow

1. **Static Content Delivery**: HTML structure loads with embedded content
2. **Style Application**: CSS variables apply theme-based styling
3. **Script Initialization**: JavaScript initializes animations and interactive features
4. **User Interaction**: Navigation clicks trigger smooth scrolling and state updates
5. **Scroll Events**: Window scroll updates navbar appearance and active navigation states

## External Dependencies

### CDN Libraries
- **Bootstrap 5.3.2**: UI framework and responsive utilities
- **Font Awesome 6.4.0**: Icon library for visual elements
- **Google Fonts**: Poppins font family for typography
- **AOS 2.3.1**: Animation library for scroll-triggered effects

### Performance Considerations
- All dependencies loaded via CDN for optimal caching
- Minimal custom JavaScript for fast loading
- CSS organized for efficient rendering
- Image optimization and lazy loading ready

## Deployment Strategy

### Static Website Hosting
- **Deployment Type**: Static file hosting (suitable for GitHub Pages, Netlify, Vercel)
- **Build Process**: No build step required - direct file serving
- **Asset Management**: All assets referenced via CDN or relative paths
- **SEO Optimization**: Meta tags, semantic HTML, and proper heading structure

### File Structure
```
/
├── index.html          # Main HTML file
├── css/
│   └── style.css      # Custom styles and theming
└── js/
    └── script.js      # Interactive functionality
```

### Browser Compatibility
- Modern browser support (ES6+ JavaScript features)
- Progressive enhancement approach
- Responsive design for all device sizes
- Graceful degradation for older browsers

### Performance Optimizations
- Minimal HTTP requests through CDN usage
- Efficient CSS with custom properties
- Lazy-loaded animations with AOS
- Optimized JavaScript execution