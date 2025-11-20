// Global Footer Component
// This file contains the footer HTML that will be injected into all pages

const footerHTML = `
    <footer class="footer">
        <svg class="footer-dot-pattern" aria-hidden="true">
            <defs>
                <pattern id="footer-dot-pattern" width="16" height="16" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" x="0" y="0">
                    <circle cx="1" cy="1" r="1.5" fill="rgba(255, 255, 255, 0.1)"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" stroke-width="0" fill="url(#footer-dot-pattern)" />
        </svg>
        <div class="footer-content">
            <div class="footer-section">
                <div class="logo" style="margin-bottom: 1rem;">
                    <img src="frontend/img/restartlabs-logos.svg" alt="RestartLab Logo" style="height: 50px; width: auto;">
                </div>
                <p>Your trusted partner for software development, mobile app development, and web development services. We're here to help you achieve digital success.</p>
                <div class="social-links" style="margin-top: 1.5rem;">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="./">Home</a>
                <a href="products">Products</a>
                <a href="company">Company</a>
                <a href="resource">Resource</a>
                <a href="get-help">Get Help</a>
            </div>
            
            <div class="footer-section">
                <h4>Services</h4>
                <a href="./#services">Software Development</a>
                <a href="./#services">Mobile App Development</a>
                <a href="./#services">Web Development</a>
                <a href="products">Our Products</a>
                <a href="get-help">Get Support</a>
            </div>
            
            <div class="footer-section">
                <h4>Contact Info</h4>
                <p><i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: var(--accent-teal);"></i> Khulna, Bangladesh</p>
                <p><i class="fas fa-phone" style="margin-right: 0.5rem; color: var(--accent-teal);"></i> +880 1XXX-XXXXXX</p>
                <p><i class="fas fa-envelope" style="margin-right: 0.5rem; color: var(--accent-teal);"></i> info@restartlab.com</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-links">
                <a href="privacy-policy">Privacy Policy</a>
                <a href="terms-conditions">Terms & Conditions</a>
            </div>
            <p>&copy; 2025 RestartLab. All rights reserved.</p>
        </div>
    </footer>
`;

// Function to inject footer into the page
function loadGlobalFooter() {
    const footerContainer = document.getElementById('global-footer');
    if (footerContainer) {
        footerContainer.innerHTML = footerHTML;
    }
}

// Auto-load footer when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGlobalFooter);
} else {
    loadGlobalFooter();
}

