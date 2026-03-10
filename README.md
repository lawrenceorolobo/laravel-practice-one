# Quizly — Enterprise Assessment Platform

An enterprise-grade online assessment and examination platform built with Laravel 12. Create, manage, and proctor assessments with real-time monitoring, fraud detection, and comprehensive analytics.

## Features

- **22 Question Types** — Single/multiple choice, true/false, fill-blank, numeric, ordering, matching, code snippet, Likert scale, shape puzzle (drag-and-drop), SVG visual patterns (Raven's Matrices), analogies, mental maths, and more
- **Visual Pattern Questions** — SVG-rendered sequence, matrix (3×3), and rotation patterns stored as JSON metadata
- **Shape Puzzle** — Duolingo-style drag-and-drop shape fitting
- **Fraud Detection** — Device fingerprinting, tab-switch detection, fullscreen monitoring, IP-based duplicate detection
- **Proctoring** — Webcam recording with Cloudinary upload, auto-end on tab switch
- **Real-Time Updates** — Laravel Reverb WebSocket broadcasting for live dashboard updates
- **Payments** — Flutterwave integration with subscription tiers
- **Email System** — Resend SMTP with per-purpose from addresses (invitations, auth, notifications, support)
- **Admin Panel** — Super admin dashboard with user management, revenue reports, feature flags
- **PDF Export** — DomPDF-powered assessment reports
- **14 Assessment Templates** — Pre-built question sets (Software Engineering, Petroleum Safety, Pattern Recognition, Healthcare, Cybersecurity, and more)

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL 8+ |
| Auth | Laravel Sanctum (token-based) |
| Cache/Queue | Redis (Predis) |
| WebSockets | Laravel Reverb |
| Payments | Flutterwave |
| Email | Resend (SMTP) |
| Proctoring | Cloudinary (webcam uploads) |
| PDF | barryvdh/laravel-dompdf |
| Frontend | Blade, Tailwind CSS 4, Vite 7 |
| Real-Time (client) | Laravel Echo + Pusher-JS |

## Requirements

- PHP 8.2 or higher
- Composer 2+
- Node.js 18+ and npm
- MySQL 8+
- Redis server (or Redis Cloud account)

## Local Setup

### 1. Clone and install dependencies

```bash
git clone https://github.com/your-username/quizly.git
cd quizly

composer install
npm install
```

### 2. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your values:

```env
# App
APP_NAME=Quizly
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quizly
DB_USERNAME=root
DB_PASSWORD=

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=database

# Redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=

# Reverb (WebSockets)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=6001
REVERB_SCHEME=http

# Mail (Resend SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=your-resend-api-key
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="admin@yourdomain.com"
MAIL_FROM_NAME="Quizly"

# Flutterwave
FLW_PUBLIC_KEY=your-public-key
FLW_SECRET_KEY=your-secret-key
FLW_ENCRYPTION_KEY=your-encryption-key
FLW_WEBHOOK_SECRET=your-webhook-secret

# Cloudinary (Proctoring)
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
CLOUDINARY_UPLOAD_PRESET=your-preset

# Timezone
APP_TIMEZONE=Africa/Lagos
```

### 3. Database setup

```bash
# Create the MySQL database first
mysql -u root -e "CREATE DATABASE quizly;"

# Run migrations
php artisan migrate

# Seed assessment templates (14 templates, 120+ questions)
php artisan db:seed --class=AssessmentTemplateSeeder
```

### 4. Start the application

Open **3 terminals**:

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Queue worker (for emails, background jobs)
php artisan queue:work

# Terminal 3 — Reverb WebSocket server (for real-time updates)
php artisan reverb:start
```

The app will be at **http://localhost:8000**.

### 5. Build frontend assets (optional for dev)

```bash
# Development (hot reload)
npm run dev

# Production build
npm run build
```

## Project Structure

```
app/
├── Http/Controllers/Api/    # 11 API controllers
│   ├── AuthController        # Register, login, OTP, password reset
│   ├── AssessmentController  # CRUD, publish, duplicate, analytics
│   ├── QuestionController    # CRUD, CSV import, reorder, batch ops
│   ├── TestController        # Test-taking flow (validate → start → answer → submit)
│   ├── InviteeController     # Candidate management, batch invite/resend
│   ├── DashboardController   # Stats, activity feed, analytics
│   ├── AdminController       # Super admin panel
│   ├── PaymentController     # Flutterwave payments
│   └── SubscriptionController
├── Models/                   # 13 Eloquent models
├── Events/                   # 4 broadcast events
├── Mail/                     # 6 mailable classes
├── Middleware/                # 6 middleware (auth, subscription, admin, rate limit, sanitize)
└── Services/                 # Fraud detection, Flutterwave

resources/views/
├── test/take.blade.php       # Test-taking UI (all 22 question types)
├── user/                     # Dashboard, assessments, analytics
├── admin/                    # Admin panel views
├── auth/                     # Login, register, OTP
├── emails/                   # Email templates
└── home.blade.php            # Landing page

routes/
├── api.php                   # API routes (public + auth + admin)
└── web.php                   # Web routes (views + callbacks)
```

## API Overview

| Group | Prefix | Auth |
|-------|--------|------|
| Test-taker | `/api/test/*` | Token in URL |
| Business admin | `/api/*` | Sanctum + Subscription |
| Super admin | `/api/admin/*` | Sanctum + Admin middleware |
| Public | `/api/subscription/plans`, `/api/contact` | None |

## License

MIT