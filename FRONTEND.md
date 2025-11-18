# 🌐 Frontend Documentation

## 📁 Frontend Structure

```
frontend/
├── assets/
│   └── styles.css          # Main CSS file
├── js/
│   ├── script.js           # Main JavaScript
│   ├── tracking.js          # Visitor tracking
│   ├── header.js            # Header component
│   └── footer.js            # Footer component
├── img/                     # Images
├── api/                     # Frontend API endpoints
│   ├── track-visit.php     # Track page visits
│   └── submit-contact.php  # Contact form submission
└── *.html                   # HTML pages
```

## 📄 Pages

### Main Pages:
- `index.html` - Homepage
- `get-help.html` - Contact/Get Help page
- `products.html` - Products page
- `company.html` - Company/About page
- `resource.html` - Resources page
- `privacy-policy.html` - Privacy Policy
- `terms-conditions.html` - Terms & Conditions

## 🎨 Styling

- **CSS File**: `frontend/assets/styles.css`
- **Fonts**: Inter & Space Grotesk (Google Fonts)
- **Icons**: Font Awesome 6.4.0
- **Colors**: 
  - Primary Teal: `#1cd4c2`
  - Dark Background: `#0a0a0a`
  - Text Primary: `#ffffff`
  - Text Secondary: `#a0a0a0`

## 🔧 JavaScript Features

### 1. Contact Form (`script.js`)
- Form validation
- AJAX submission
- Success/Error notifications
- Inline message display

### 2. Visitor Tracking (`tracking.js`)
- Page visit tracking
- Session tracking
- Time spent tracking
- Sends data to `api/track-visit.php`

### 3. Header/Footer (`header.js`, `footer.js`)
- Dynamic header/footer loading
- Navigation menu
- Mobile responsive

## 📡 API Endpoints

### 1. Track Visit
- **URL**: `frontend/api/track-visit.php`
- **Method**: GET
- **Purpose**: Track page visits and visitor data
- **Data Sent**: Page URL, IP, User Agent, etc.

### 2. Submit Contact
- **URL**: `frontend/api/submit-contact.php`
- **Method**: POST
- **Purpose**: Handle contact form submissions
- **Data Sent**: Name, Email, Phone, Company, Message
- **Response**: JSON with success/error status

## 🚀 Access URLs

### Localhost:
- Home: `http://localhost/restartlabs/frontend/`
- Get Help: `http://localhost/restartlabs/frontend/get-help.html`
- Products: `http://localhost/restartlabs/frontend/products.html`
- Company: `http://localhost/restartlabs/frontend/company.html`

### Production:
- Update `base href` in HTML files from `/restartlabs/frontend/` to `/`
- Update API paths if needed

## 📝 Features

### Contact Form:
- ✅ Real-time validation
- ✅ AJAX submission (no page reload)
- ✅ Success/Error notifications
- ✅ Inline message display
- ✅ Email notification (production)
- ✅ WhatsApp notification (if enabled)
- ✅ Database storage

### Visitor Tracking:
- ✅ Page visit tracking
- ✅ IP address detection
- ✅ User agent detection
- ✅ Bot detection
- ✅ Session tracking
- ✅ Geolocation (optional)

## 🔗 Integration

### Backend Integration:
- Uses shared `config/` files
- Uses shared `includes/` files
- Connects to same database
- Admin panel can view all data

### API Integration:
- All API calls use relative paths
- CORS enabled for cross-origin requests
- JSON responses for all endpoints

## 🛠️ Development

### Local Development:
1. Place files in `frontend/` folder
2. Access via `http://localhost/restartlabs/frontend/`
3. Check browser console for errors
4. Test all forms and links

### Production Deployment:
1. Update `base href` in all HTML files
2. Update API paths if domain changes
3. Test email notifications
4. Verify visitor tracking
5. Check all links and navigation

## 📱 Responsive Design

- ✅ Mobile responsive
- ✅ Tablet optimized
- ✅ Desktop optimized
- ✅ Touch-friendly navigation
- ✅ Mobile menu

## 🔒 Security

- ✅ CSRF protection (via security.php)
- ✅ Input sanitization
- ✅ XSS prevention
- ✅ SQL injection prevention
- ✅ IP blocking support

## 📊 Analytics

- Visitor tracking data stored in database
- Viewable in admin panel
- Daily statistics
- Bot vs Real visitor detection

## 🐛 Troubleshooting

### Contact Form Not Working?
- Check browser console for errors
- Verify API endpoint is accessible
- Check database connection
- Verify form validation

### Tracking Not Working?
- Check `tracking.js` is loaded
- Verify `api/track-visit.php` is accessible
- Check browser console for errors
- Verify database connection

### Styles Not Loading?
- Check `base href` is correct
- Verify CSS file path: `assets/styles.css`
- Check browser console for 404 errors
- Clear browser cache

## 📞 Support

For issues or questions:
- Check browser console for errors
- Check PHP error logs
- Verify file paths
- Test API endpoints directly

