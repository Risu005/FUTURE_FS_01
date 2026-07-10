# Personal Professional Portfolio Website

**Task 1 — Future Interns Full Stack Web Development Internship (2026)**

A modern, interactive personal portfolio website built with **HTML, CSS, JavaScript (frontend)** and **PHP / MySQL (backend)**. This portfolio showcases my skills, projects, professional experience, and includes a fully functional contact form with email notifications, database storage, and an admin dashboard.

---

## Live Demo

[View Live Portfolio](https://risu005.github.io/FUTURE_FS_01)

---

## Features

### Frontend

- **Responsive Design** — Fully responsive across all devices (mobile, tablet, desktop) with custom breakpoints at 1024px and 768px
- **Modern Dark Theme** — Sleek dark UI with gradient accents, glassmorphism effects, and CSS custom properties for easy theming
- **Animated Background** — Floating orbs with smooth CSS keyframe animations
- **Interactive Code Window** — Syntax-highlighted code snippet display in the hero section with typing cursor animation
- **Scroll Animations** — Elements fade in as you scroll using the Intersection Observer API
- **Smooth Scrolling** — Navigation links smoothly scroll to sections
- **Mobile Navigation** — Hamburger menu with ARIA accessibility for mobile devices
- **Skill Progress Bars** — Animated progress bars that fill on scroll
- **Timeline Experience** — Visual timeline for education and work history with status badges
- **Breadcrumb Navigation** — On inner pages for improved UX and SEO

### Backend (PHP)

- **Contact Form Handler** — Secure form processing with server-side validation
- **Database Storage** — MySQL integration with PDO prepared statements for storing contact messages
- **Admin Dashboard** — Secure login-protected message viewer with read/unread tracking, statistics, and delete functionality
- **Input Sanitization** — XSS protection via `htmlspecialchars()`
- **Email Validation** — Strict email format validation with `FILTER_VALIDATE_EMAIL`
- **Rate Limiting** — 1-minute cooldown between submissions (session-based)
- **Honeypot Field** — Anti-spam protection
- **Fallback Logging** — Messages saved to file if both `mail()` and database fail
- **JSON Response** — AJAX-friendly JSON responses for seamless UX

### SEO & Accessibility

- Meta tags (title, description, keywords, author) on every page
- Open Graph tags for social sharing
- Semantic HTML5 structure with proper heading hierarchy
- Accessible ARIA labels, roles, and `sr-only` text throughout
- Keyboard-navigable interactive elements

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP 7.4+ |
| Database | MySQL (InnoDB, utf8mb4) |
| Icons | Font Awesome 6.5.1 |
| Fonts | Inter, JetBrains Mono (Google Fonts) |

---

## File Structure

```
FUTURE_FS_01/
├── index.html              # Homepage with hero, stats, about preview, projects preview, CTA
├── about.html              # About me page with bio, skills cards, and development approach
├── skills.html             # Technical skills with progress bars, tools grid, and learning section
├── projects.html           # Featured projects showcase with status badges and metadata
├── experience.html         # Professional timeline with education and work history
├── contact.html            # Contact form with info cards and social links
├── contact.php             # Contact form handler with validation, DB storage, and email
├── admin-messages.php      # Admin dashboard for viewing and managing contact messages
├── style.css               # Complete stylesheet with CSS variables and responsive queries
├── portfolio_db.sql        # MySQL database schema for contact_messages and admin_users tables
└── README.md               # This file
```

---

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/FUTURE_FS_01.git
cd FUTURE_FS_01
```

### 2. Configure the Database

Import the database schema:

```bash
mysql -u root -p < portfolio_db.sql
```

Or use phpMyAdmin to import `portfolio_db.sql`.

### 3. Configure Contact Form

Open `contact.php` and update the recipient email:

```php
$recipient_email = 'your-real-email@example.com';  // CHANGE THIS
```

Update database credentials in both `contact.php` and `admin-messages.php` if needed:

```php
$db_host = 'localhost';
$db_name = 'portfolio_db';
$db_user = 'root';
$db_pass = '';
```

### 4. Deploy Frontend (GitHub Pages)

```bash
# Push to GitHub
git add .
git commit -m "Initial portfolio commit"
git push origin main

# Enable GitHub Pages in repository settings
# Set source to "Deploy from a branch" -> "main" -> "/ (root)"
```

> **Note:** GitHub Pages only serves static files. The PHP backend (`contact.php`, `admin-messages.php`) requires a PHP-enabled server.

### 5. Deploy Backend (PHP Host)

For the contact form and admin dashboard to work, you need a PHP-enabled server:

**Option A: Free PHP Hosting**
- [InfinityFree](https://infinityfree.net/)
- [000webhost](https://www.000webhost.com/)
- [Freehostia](https://www.freehostia.com/)

**Option B: Local Development**

```bash
# Using PHP built-in server
php -S localhost:8000

# Or using XAMPP/WAMP/MAMP
# Place files in htdocs/www directory
```

**Option C: Paid Hosting**
- Any shared hosting with PHP support (Hostinger, Bluehost, SiteGround, etc.)

### 6. Update Form Action (if backend is on different domain)

If your PHP backend is hosted separately from the frontend, update the form action in `contact.html`:

```html
<form id="contactForm" action="https://your-php-host.com/contact.php" method="POST" novalidate>
```

---

## Admin Dashboard

Access the admin panel at:

```
https://your-domain.com/admin-messages.php
```

**Default login password:** `@Logankent001`

> **Important:** Change this password before deploying to production.

The admin dashboard provides:
- Total, unread, and read message counts
- Message listing with status badges (New / Read)
- Mark-as-read and delete actions
- Direct reply links via `mailto:`
- Mobile-responsive table layout

---

## Why PHP for the Backend?

Given the developer's existing expertise in PHP and the nature of Task 1, PHP was chosen for these reasons:

1. **Familiarity** — Existing PHP proficiency ensures higher code quality and faster development
2. **Simplicity** — PHP's built-in `mail()` function handles email without external dependencies
3. **Native Support** — Most shared hosting supports PHP out of the box
4. **No Build Step** — PHP runs directly without npm, webpack, or compilation
5. **Portfolio Consistency** — Aligns with the developer's RFID inventory system (also PHP-based)

---

## Customization Guide

### Update Personal Information

Edit these sections across the HTML files:

| Section | File | Description |
|---------|------|-------------|
| Hero Section | `index.html` | Name, title, description |
| About Section | `about.html` | Bio, location, social links |
| Skills Section | `skills.html` | Skill names and percentages |
| Projects Section | `projects.html` | Project cards with your real projects |
| Experience Section | `experience.html` | Timeline entries |
| Contact Section | `contact.html` | Email, phone, location |
| Footer | All pages | Copyright, social links |

### Update Colors

Modify CSS custom properties in `:root` within `style.css`:

```css
:root {
    --primary: #6366f1;        /* Main accent color */
    --secondary: #06b6d4;      /* Secondary accent */
    --accent: #f59e0b;         /* Highlight color */
    --bg: #0f0f1a;             /* Background */
    --bg-card: #16162a;        /* Card background */
    --text: #e2e8f0;           /* Main text */
}
```

### Add Real Projects

Replace or add project cards in `projects.html`:

```html
<div class="reveal project-card">
    <div class="project-image">
        <i class="fas fa-rocket" aria-hidden="true"></i>
        <span class="project-status active"><i class="fas fa-circle" aria-hidden="true"></i> Active</span>
    </div>
    <div class="project-content">
        <div class="project-tags">
            <span class="project-tag">PHP</span>
            <span class="project-tag">MySQL</span>
        </div>
        <h3 class="project-title">Your Project Name</h3>
        <p class="project-desc">Description of your project...</p>
        <div class="project-links">
            <a href="https://github.com/you/repo" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-github" aria-hidden="true"></i> View Code
            </a>
        </div>
        <div class="project-meta">
            <div class="project-meta-item"><span>Status</span><p class="success">In Development</p></div>
            <div class="project-meta-item"><span>Role</span><p>Lead Developer</p></div>
        </div>
    </div>
</div>
```

---

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Opera 76+

---

## Performance

- **No external CSS/JS frameworks** — Pure vanilla for minimal load time
- **Google Fonts** loaded with `display=swap` for fast text rendering
- **Font Awesome** loaded from CDN with caching
- **Optimized animations** using CSS transforms (GPU accelerated)
- **Lazy loading** ready for images (add `loading="lazy"` to `<img>` tags)

---

## License

This project is created for educational purposes as part of the **Future Interns Full Stack Web Development Internship**.

---

## Author

**Derick Mgalawe** — Full Stack Developer & RFID Systems Engineer
- Ardhi University, Tanzania
- [GitHub](https://github.com/Risu005)
- [LinkedIn](https://www.linkedin.com/in/derick-mgalawe-052174206/)
- Email: derickgilbert001@gmail.com

---

## Acknowledgments

- **Future Interns** for the internship opportunity
- **Font Awesome** for icons
- **Google Fonts** for typography
