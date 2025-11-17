// ============================================
// RestartLab - JavaScript Functionality
// ============================================

// Mobile Menu Toggle - Re-initialize after header loads
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');

    if (mobileMenuBtn && navLinks) {
        // Check if already initialized
        if (mobileMenuBtn.dataset.initialized === 'true') {
            return;
        }
        
        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            navLinks.classList.toggle('active');
            const icon = mobileMenuBtn.querySelector('i');
            if (icon) {
                if (navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
        
        // Mark as initialized
        mobileMenuBtn.dataset.initialized = 'true';
    }
}

// Initialize multiple times to catch header loading
function setupMobileMenu() {
    initMobileMenu();
    // Try again after a delay
    setTimeout(initMobileMenu, 100);
    setTimeout(initMobileMenu, 500);
    setTimeout(initMobileMenu, 1000);
}

// Initialize after DOM and header are loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupMobileMenu);
} else {
    setupMobileMenu();
}

// Also watch for header container changes
const headerContainer = document.getElementById('global-header');
if (headerContainer) {
    const observer = new MutationObserver(() => {
        setTimeout(initMobileMenu, 100);
    });
    observer.observe(headerContainer, { childList: true, subtree: true });
}

// Listen for header loaded event
window.addEventListener('headerLoaded', () => {
    setTimeout(initMobileMenu, 100);
});

// Mega Menu for Mobile and Desktop
const megaMenuParents = document.querySelectorAll('.mega-menu-parent');
megaMenuParents.forEach(parent => {
    const link = parent.querySelector('a');
    if (link) {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.innerWidth <= 768) {
                parent.classList.toggle('active');
            }
        });
    }
});

// Navbar Scroll Effect
function initNavbarScroll() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;
    
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // Check initial scroll position
    if (window.pageYOffset > 100) {
        navbar.classList.add('scrolled');
    }
}

// Initialize after DOM is ready and header is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initNavbarScroll, 100);
    });
} else {
    setTimeout(initNavbarScroll, 100);
}

// Smooth Scrolling for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        
        if (target) {
            const offsetTop = target.offsetTop - 80; // Account for fixed navbar
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            if (navLinks && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            }
        }
    });
});

// Active Navigation Link Highlighting
const sections = document.querySelectorAll('section[id]');
const navAnchors = document.querySelectorAll('.nav-links a[href^="#"]');

window.addEventListener('scroll', () => {
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        
        if (window.pageYOffset >= sectionTop - 200) {
            current = section.getAttribute('id');
        }
    });
    
    navAnchors.forEach(anchor => {
        anchor.classList.remove('active');
        if (anchor.getAttribute('href') === `#${current}`) {
            anchor.classList.add('active');
        }
    });
});

// Contact Form Submission
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const name = this.querySelector('#name').value;
        const email = this.querySelector('#email').value;
        const phone = this.querySelector('#phone').value;
        const company = this.querySelector('#company').value;
        const message = this.querySelector('#message').value;
        
        // Simple validation
        if (!name || !email || !phone || !message) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }
        
        // Simulate form submission
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitButton.disabled = true;
        
        // Simulate API call (replace with actual API endpoint)
        setTimeout(() => {
            showNotification('Thank you! Your message has been sent successfully. We will contact you soon.', 'success');
            this.reset();
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 2000);
    });
}

// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    
    if (question) {
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all FAQ items
            faqItems.forEach(faqItem => {
                faqItem.classList.remove('active');
                const icon = faqItem.querySelector('.faq-icon');
                if (icon) icon.textContent = '+';
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
                const icon = item.querySelector('.faq-icon');
                if (icon) icon.textContent = '−';
            }
        });
    }
});

// Scroll Animation
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.service-card, .stat-card, .testimonial-card, .portfolio-category, .tech-item');
    
    animatedElements.forEach((el) => {
        observer.observe(el);
    });
});

// Notification System
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? 'var(--accent-teal)' : '#ffffff'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;
    notification.textContent = message;
    
    // Add animation styles if not already added
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Portfolio Category Hover Effect
const portfolioCategories = document.querySelectorAll('.portfolio-category');

portfolioCategories.forEach(category => {
    category.addEventListener('click', function() {
        // You can add navigation or filtering logic here
    });
});

// Technology Stack Hover Effect
const techItems = document.querySelectorAll('.tech-item');

techItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.05)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Add loading animation
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// Parallax effect for hero section (disabled - causing scroll issues)
// let heroParallax = false;
// if (window.innerWidth > 768) {
//     heroParallax = true;
//     window.addEventListener('scroll', () => {
//         const hero = document.querySelector('.hero');
//         if (hero) {
//             const scrolled = window.pageYOffset;
//             const rate = scrolled * 0.5;
//             hero.style.transform = `translateY(${rate}px)`;
//         }
//     });
// }

// Mobile menu styles are now in styles.css
