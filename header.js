// Global Header Component
// This file contains the header/navbar HTML that will be injected into all pages

function loadGlobalHeader() {
    const headerContainer = document.getElementById('global-header');
    if (!headerContainer) return;
    
    // Get current page to set active class
    const currentPath = window.location.pathname;
    const currentPage = currentPath.split('/').pop() || 'index.html';
    
    // Determine active page
    let activeHome = '';
    let activeProducts = '';
    let activeCompany = '';
    let activeResource = '';
    let activeGetHelp = '';
    
    if (currentPage === '' || currentPage === 'index.html' || currentPage === 'index') {
        activeHome = 'active';
    } else if (currentPage === 'products' || currentPage === 'products.html') {
        activeProducts = 'active';
    } else if (currentPage === 'company' || currentPage === 'company.html') {
        activeCompany = 'active';
    } else if (currentPage === 'resource' || currentPage === 'resource.html') {
        activeResource = 'active';
    } else if (currentPage === 'get-help' || currentPage === 'get-help.html') {
        activeGetHelp = 'active';
    }
    
    const headerHTML = `
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="./" class="logo">
                <img src="img/restartlabs-logos.svg" alt="RestartLab Logo" style="height: 40px; width: auto;">
            </a>
            
            <ul class="nav-links" id="navLinks">
                <li><a href="./" class="${activeHome}">Home</a></li>
                <li class="mega-menu-parent">
                    <a href="products" class="${activeProducts}">Products <i class="fas fa-chevron-down"></i></a>
                    <div class="mega-menu">
                        <div class="mega-menu-header">
                            <h3>Products</h3>
                        </div>
                        <div class="mega-menu-content">
                            <div class="mega-menu-products">
                                <div class="mega-product-card">
                                    <div class="mega-product-icon">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <h5>StocksFlow Inventory Management</h5>
                                    <p>Comprehensive inventory management solution for businesses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="company" class="${activeCompany}">Company</a></li>
                <li><a href="resource" class="${activeResource}">Resource</a></li>
                <li><a href="get-help" class="${activeGetHelp}">Get Help</a></li>
                <li><a href="get-help" class="btn-primary">Get a Quote</a></li>
            </ul>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    `;
    
    headerContainer.innerHTML = headerHTML;
    
    // Re-initialize mobile menu functionality after header is loaded
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
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
    }
}

// Auto-load header when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGlobalHeader);
} else {
    loadGlobalHeader();
}

