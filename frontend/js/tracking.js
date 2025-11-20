/**
 * Client-side Visitor Tracking
 * This script tracks page views and sends data to the server
 */

(function() {
    'use strict';
    
    // Get current page
    const page = window.location.pathname;
    
    // Track visit via API
    fetch('frontend/api/track-visit.php?page=' + encodeURIComponent(page), {
        method: 'GET',
        credentials: 'same-origin'
    }).catch(function(error) {
        // Silently fail if tracking fails
        console.log('Tracking failed:', error);
    });
    
    // Track page visibility changes (to detect real user behavior)
    let startTime = Date.now();
    let isVisible = true;
    
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            isVisible = false;
        } else {
            isVisible = true;
            startTime = Date.now();
        }
    });
    
    // Track when user leaves page
    window.addEventListener('beforeunload', function() {
        const timeSpent = Math.floor((Date.now() - startTime) / 1000);
        if (timeSpent > 0) {
            // Send time spent (optional, can be implemented in backend)
            navigator.sendBeacon('frontend/api/track-visit.php?page=' + encodeURIComponent(page) + '&duration=' + timeSpent);
        }
    });
})();

