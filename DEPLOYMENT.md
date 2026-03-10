# Quizly — Deployment Guide

How to deploy Quizly to a live server with a Namecheap domain.

---

## What You Need

| Service | Purpose | Cost |
|---------|---------|------|
| **VPS** (DigitalOcean / Hostinger / Contabo) | Host the app | ~$5-12/mo |
| **Namecheap domain** | Your domain (e.g. quizly.com) | ~$10/yr |
| **Redis Cloud** (or install on VPS) | Cache + queue | Free tier works |
| **Resend** | Email delivery | Free up to 3k/mo |
| **Flutterwave** | Payments | Free (they take tx fee) |
| **Cloudinary** | Webcam recording storage | Free tier works |

---

## Step 1: Get a VPS

Pick a VPS with at least **1GB RAM, 1 CPU, 25GB SSD**. Ubuntu 22.04 or 24.04.

Recommended:
- DigitalOcean $6/mo droplet
- Hostinger VPS
- Contabo VPS

Once you have the VPS, SSH in:

```bash
ssh root@your-server-ip
```

## Step 2: Install Server Software

```bash
# Update system
apt update && apt upgrade -y

# PHP 8.2 + extensions
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-redis php8.2-gd

# Nginx
apt install -y nginx

# MySQL
apt install -y mysql-server
mysql_secure_installation

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Supervisor (keeps queue worker + reverb running)
apt install -y supervisor

# Certbot (free SSL)
apt install -y certbot python3-certbot-nginx
```

## Step 3: Create MySQL Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE quizly;
CREATE USER 'quizly_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL ON quizly.* TO 'quizly_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 4: Deploy Your Code

```bash
# Create app directory
mkdir -p /var/www/quizly
cd /var/www/quizly

# Clone your repo
git clone https://github.com/your-username/quizly.git .

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Set permissions
chown -R www-data:www-data /var/www/quizly
chmod -R 755 /var/www/quizly
chmod -R 775 storage bootstrap/cache
```

## Step 5: Configure Environment

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Update these values in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=quizly
DB_USERNAME=quizly_user
DB_PASSWORD=your_strong_password

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=file

# Redis (use your Redis Cloud credentials or local)
REDIS_CLIENT=predis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=your-redis-port

# Reverb (WebSockets) — for production
REVERB_HOST=yourdomain.com
REVERB_PORT=6001
REVERB_SCHEME=https

# Mail, Flutterwave, Cloudinary — same as local
```

Then run:

```bash
php artisan migrate --force
php artisan db:seed --class=AssessmentTemplateSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 6: Nginx Config

```bash
nano /etc/nginx/sites-available/quizly
```

Paste this:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/quizly/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSocket proxy for Reverb
    location /app {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 60s;
    }
}
```

Enable it:

```bash
ln -s /etc/nginx/sites-available/quizly /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx
```

## Step 7: Point Your Namecheap Domain

1. Log into **Namecheap** → Domain List → Manage your domain
2. Go to **Advanced DNS**
3. Add these records:

| Type | Host | Value | TTL |
|------|------|-------|-----|
| A | @ | `your-server-ip` | Automatic |
| A | www | `your-server-ip` | Automatic |

4. Wait 5-30 minutes for DNS to propagate

## Step 8: SSL Certificate (Free)

```bash
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Follow the prompts. Certbot auto-renews. Your site is now HTTPS.

## Step 9: Background Processes (Supervisor)

Queue worker and Reverb need to run 24/7. Supervisor keeps them alive.

```bash
nano /etc/supervisor/conf.d/quizly-worker.conf
```

```ini
[program:quizly-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/quizly/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/quizly/storage/logs/worker.log
stopwaitsecs=3600

[program:quizly-reverb]
process_name=%(program_name)s
command=php /var/www/quizly/artisan reverb:start --host=127.0.0.1 --port=6001
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/quizly/storage/logs/reverb.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start all
```

---

## Updating the App (After Changes)

```bash
cd /var/www/quizly
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart all
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 error | `tail -f storage/logs/laravel.log` |
| Permissions | `chown -R www-data:www-data storage bootstrap/cache` |
| Queue not processing | `supervisorctl status` and check worker logs |
| WebSockets not connecting | Check Nginx `/app` proxy block and Reverb supervisor |
| Emails not sending | Verify Resend API key in `.env` and check queue worker |
| CSS not loading | Run `npm run build` and clear browser cache |

---

## Services Setup Summary

| Service | Where to sign up | What you need |
|---------|-----------------|---------------|
| Resend | resend.com | API key, verified domain |
| Flutterwave | flutterwave.com | Public key, secret key, webhook secret |
| Cloudinary | cloudinary.com | Cloud name, API key, API secret, upload preset |
| Redis Cloud | redis.com | Host, port, password (or install locally) |
