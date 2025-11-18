# 🔐 Admin Panel Documentation

## 📁 Admin Panel Structure

```
admin/
├── assets/
│   └── admin.css          # Admin panel styles
├── api/                    # Admin API endpoints
│   ├── login.php          # Admin login
│   ├── block-ip.php       # Block IP address
│   ├── unblock-ip.php     # Unblock IP address
│   ├── update-contact-status.php  # Update contact status
│   ├── save-settings.php  # Save settings
│   └── get-settings.php   # Get settings
├── includes/
│   ├── auth.php           # Authentication check
│   ├── header.php         # Admin header/navigation
│   └── footer.php         # Admin footer
├── index.php              # Login page
├── dashboard.php          # Main dashboard
├── visitors.php           # Visitor management
├── contacts.php           # Contact form submissions
├── ip-management.php     # IP blocking management
├── settings.php           # System settings
└── security.php           # Security logs (hidden)
```

## 🔑 Login

### Default Credentials:
- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change password after first login!

### Login URL:
- Localhost: `http://localhost/restartlabs/admin/`
- Production: `https://yourdomain.com/admin/`

## 📊 Dashboard Features

### Statistics Display:
- ✅ Total Visitors
- ✅ Today's Visitors
- ✅ Total Contact Submissions
- ✅ New Contact Submissions
- ✅ Blocked IPs
- ✅ Daily Statistics Chart

### Quick Actions:
- View recent visitors
- View new contacts
- Quick IP block
- System status

## 👥 Visitor Management

### Features:
- ✅ View all visitors
- ✅ Filter by date, IP, bot status
- ✅ View visitor details:
  - IP Address
  - User Agent
  - Page Visited
  - Visit Time
  - Country/City
  - Device Type
  - Browser/OS
- ✅ Block suspicious IPs
- ✅ Export data (future)

### Access:
- URL: `admin/visitors.php`
- Shows paginated visitor list
- Search and filter options

## 📧 Contact Form Management

### Features:
- ✅ View all contact submissions
- ✅ Filter by status (new, read, replied)
- ✅ View submission details:
  - Name, Email, Phone
  - Company
  - Message
  - IP Address
  - Submission Time
- ✅ Mark as read/replied
- ✅ Add notes
- ✅ Block IP from contact form
- ✅ View WhatsApp sent status

### Access:
- URL: `admin/contacts.php`
- Badge shows new submissions count
- Status filter available

## 🚫 IP Management

### Features:
- ✅ View all blocked IPs
- ✅ Block new IP addresses
- ✅ Unblock IP addresses
- ✅ View block reason
- ✅ See who blocked and when
- ✅ Active/Inactive status

### Access:
- URL: `admin/ip-management.php`
- Manual IP blocking
- Auto-block suspicious IPs (optional)

## ⚙️ Settings

### WhatsApp Configuration:
- ✅ WhatsApp Number
- ✅ Enable/Disable WhatsApp notifications
- ✅ Contact Email address
- ✅ Save settings to database

### Access:
- URL: `admin/settings.php`
- Settings stored in `settings` table
- Real-time updates

## 🔒 Security Features

### Authentication:
- ✅ Session-based login
- ✅ Password hashing (bcrypt)
- ✅ Session timeout (1 hour)
- ✅ Login attempt tracking
- ✅ Auto-logout on timeout

### Security Logs:
- ✅ Attack detection logs
- ✅ SQL injection attempts
- ✅ XSS attempts
- ✅ Suspicious activity
- ⚠️ **Note**: Security logs page is hidden (not needed)

## 🗄️ Database Tables

### Used Tables:
- `admins` - Admin users
- `visitors` - Visitor data
- `contact_submissions` - Contact forms
- `blocked_ips` - Blocked IPs
- `attack_logs` - Security logs
- `daily_stats` - Daily statistics
- `visitor_sessions` - Session tracking
- `settings` - System settings

## 🔧 Configuration

### Config Files (Shared):
- `config/config.php` - General settings
- `config/database.php` - Database connection
- `config/security.php` - Security functions

### Settings (Database):
- WhatsApp number
- WhatsApp enabled
- Contact email
- Other system settings

## 📡 API Endpoints

### Admin APIs:

#### 1. Login
- **URL**: `admin/api/login.php`
- **Method**: POST
- **Data**: username, password
- **Response**: JSON with success/error

#### 2. Block IP
- **URL**: `admin/api/block-ip.php`
- **Method**: POST
- **Data**: ip_address, reason
- **Response**: JSON

#### 3. Unblock IP
- **URL**: `admin/api/unblock-ip.php`
- **Method**: POST
- **Data**: ip_address
- **Response**: JSON

#### 4. Update Contact Status
- **URL**: `admin/api/update-contact-status.php`
- **Method**: POST
- **Data**: id, status, notes
- **Response**: JSON

#### 5. Save Settings
- **URL**: `admin/api/save-settings.php`
- **Method**: POST
- **Data**: whatsapp_number, whatsapp_enabled, contact_email
- **Response**: JSON

#### 6. Get Settings
- **URL**: `admin/api/get-settings.php`
- **Method**: GET
- **Response**: JSON with settings

## 🎨 UI Features

### Design:
- ✅ Dark theme
- ✅ Modern UI
- ✅ Responsive design
- ✅ Mobile-friendly
- ✅ Icon-based navigation
- ✅ Color-coded status badges

### Navigation:
- Dashboard
- Visitors
- Contact Forms
- IP Management
- Settings
- Logout

## 📊 Statistics

### Dashboard Stats:
- Total visitors count
- Today's visitors
- Contact submissions
- New contacts badge
- Daily chart
- Recent activity

## 🔔 Notifications

### Contact Form:
- ✅ Email notification (production)
- ✅ WhatsApp notification (if enabled)
- ✅ Database storage
- ✅ Admin panel display

### Email:
- HTML formatted emails
- WhatsApp reply link
- Admin panel link
- All contact details

### WhatsApp:
- Formatted messages
- Contact details
- IP address
- Time stamp
- Logged to file (localhost)

## 🛠️ Development

### File Structure:
- All admin files in `admin/` folder
- Shared config in `config/` folder
- Shared includes in `includes/` folder
- API endpoints in `admin/api/` folder

### Authentication:
- `admin/includes/auth.php` - Required on all pages
- Session check
- Timeout check
- Auto-redirect if not logged in

## 🔒 Security Best Practices

1. ✅ Change default password
2. ✅ Use strong passwords
3. ✅ Regular backups
4. ✅ Review security logs
5. ✅ Keep PHP/MySQL updated
6. ✅ Limit login attempts
7. ✅ Session timeout enabled

## 🐛 Troubleshooting

### Can't Login?
- Check username/password
- Verify database connection
- Check PHP error logs
- Clear browser cookies

### Dashboard Not Loading?
- Check database connection
- Verify all tables exist
- Check PHP error logs
- Verify file permissions

### Settings Not Saving?
- Check database connection
- Verify `settings` table exists
- Check admin authentication
- Verify API endpoint

### Contact Forms Not Showing?
- Check database connection
- Verify `contact_submissions` table exists
- Check PHP error logs
- Verify data exists

## 📞 Support

For issues:
- Check PHP error logs
- Check browser console
- Verify database connection
- Test API endpoints
- Check file permissions

## 🚀 Deployment

### Production Checklist:
1. Change default admin password
2. Update database credentials
3. Disable error display
4. Enable HTTPS
5. Configure email settings
6. Test all features
7. Backup database
8. Review security settings

