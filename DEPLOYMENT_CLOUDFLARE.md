# Deploying FizzBoss to Cloudflare (with frizzboss.ca)

## Understanding Cloudflare + Laravel Hosting

**Important:** Cloudflare itself doesn't host Laravel apps. Here's what you need:

1. **Web Server** - Hosts your Laravel application (DigitalOcean, AWS, etc.)
2. **Cloudflare** - Provides DNS, CDN, SSL, security (sits in front of your server)

Your domain `frizzboss.ca` will point to Cloudflare → Cloudflare forwards to your server.

---

## Option 1: Quick & Easy (Recommended for Beginners)

### Use Laravel Forge + DigitalOcean + Cloudflare

**Cost:** ~$12-17/month ($12 DigitalOcean + $5 Forge)

**What is Laravel Forge?**
- Automated Laravel deployment tool
- Handles server setup, deployments, SSL, etc.
- Made by Laravel creator Taylor Otwell

### Steps:

#### 1. Get a Server (DigitalOcean Droplet)

**a) Sign up:** https://www.digitalocean.com/
- Use promo code for $200 free credit (Google "DigitalOcean promo")

**b) Create a Droplet:**
- Click "Create" → "Droplets"
- Choose: Ubuntu 24.04 LTS
- Plan: Basic ($12/month - 2GB RAM)
- Datacenter: Choose closest to your users (e.g., Toronto for Canada)
- Authentication: SSH key (Forge will add this)
- Click "Create Droplet"

**Note your server IP address** (e.g., `142.93.123.45`)

#### 2. Set Up Laravel Forge

**a) Sign up:** https://forge.laravel.com/
- $12/month plan (first server free trial available)

**b) Connect DigitalOcean:**
- Forge → Account → Source Control
- Click "DigitalOcean" → Add your API token

**c) Create Server in Forge:**
- Servers → Create Server
- Choose DigitalOcean
- Server name: frizzboss
- Server size: 2GB
- Region: Same as your droplet
- PHP version: 8.4
- Database: PostgreSQL
- Click "Create Server"

**Wait 5-10 minutes** for Forge to provision the server.

#### 3. Deploy Your Site on Forge

**a) Create Site:**
- Sites → New Site
- Root Domain: `frizzboss.ca`
- Project Type: Laravel
- Click "Add Site"

**b) Connect GitHub Repository:**
- Site → Apps → GitHub
- Repository: `Quigybobo/lilasaporitowebsite`
- Branch: `main`
- Install Repository

**c) Configure Environment:**
- Site → Environment
- Update `.env` values:
  - `APP_URL=https://frizzboss.ca`
  - Add your Google OAuth credentials
  - Add your Stripe keys
  - Database credentials (Forge auto-generates these)

**d) Deploy:**
- Site → Deployments
- Click "Deploy Now"

#### 4. Set Up Cloudflare DNS

**a) Add Site to Cloudflare:**
- Go to: https://dash.cloudflare.com/
- Click "Add a site"
- Enter: `frizzboss.ca`
- Choose Free plan
- Click "Add site"

**b) Update DNS Records:**

Click "DNS" → "Records" → Add these:

| Type | Name | Content | Proxy Status |
|------|------|---------|--------------|
| A    | @    | YOUR_SERVER_IP | Proxied (orange cloud) |
| A    | www  | YOUR_SERVER_IP | Proxied (orange cloud) |

Replace `YOUR_SERVER_IP` with your DigitalOcean droplet IP.

**c) Update Nameservers at Your Domain Registrar:**

Cloudflare will show you nameservers like:
```
fred.ns.cloudflare.com
lucy.ns.cloudflare.com
```

Go to where you bought `frizzboss.ca` and update the nameservers to these.

**Wait 24-48 hours** for DNS propagation (usually much faster).

#### 5. Enable SSL (HTTPS)

**In Cloudflare:**
- SSL/TLS → Overview
- Set to: "Full (strict)"

**In Forge:**
- Site → SSL
- Click "Let's Encrypt"
- Enable both `frizzboss.ca` and `www.frizzboss.ca`
- Click "Obtain Certificate"

---

## Option 2: Manual Deployment (Advanced)

### Use Any VPS + Cloudflare

If you want full control:

#### 1. Get a VPS

Choose one:
- **DigitalOcean** - $12/month (2GB droplet)
- **Linode/Akamai** - $12/month
- **Vultr** - $12/month
- **Hetzner** - €4.51/month (cheapest, EU servers)

#### 2. Server Setup

SSH into your server and run:

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.4 and extensions
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common \
  php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-curl \
  php8.4-zip php8.4-gd php8.4-redis php8.4-intl php8.4-bcmath

# Install Nginx
sudo apt install -y nginx

# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Install Git
sudo apt install -y git
```

#### 3. Clone Your Repository

```bash
cd /var/www
sudo git clone https://github.com/Quigybobo/lilasaporitowebsite.git frizzboss
sudo chown -R www-data:www-data frizzboss
cd frizzboss
```

#### 4. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### 5. Configure Database

```bash
sudo -u postgres psql

# In PostgreSQL:
CREATE DATABASE frizzboss;
CREATE USER frizzboss_user WITH PASSWORD 'secure_password_here';
GRANT ALL PRIVILEGES ON DATABASE frizzboss TO frizzboss_user;
\q
```

#### 6. Configure Environment

```bash
cp .env.example .env
nano .env
```

Update:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://frizzboss.ca

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=frizzboss
DB_USERNAME=frizzboss_user
DB_PASSWORD=secure_password_here

# Add Google OAuth
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_secret
GOOGLE_REDIRECT_URI=https://frizzboss.ca/auth/google/callback

# Add Stripe keys
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 7. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/frizzboss
```

Paste this configuration:

```nginx
server {
    listen 80;
    server_name frizzboss.ca www.frizzboss.ca;
    root /var/www/frizzboss/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/frizzboss /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 8. Set Up Cloudflare (Same as Option 1 Step 4)

Follow the Cloudflare DNS steps above.

#### 9. Install SSL Certificate

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d frizzboss.ca -d www.frizzboss.ca
```

---

## Option 3: Cloudflare Pages (For Static Sites Only)

**Note:** This won't work for Laravel since it's a PHP application. Cloudflare Pages only hosts static HTML/JS sites.

---

## Recommended: Option 1 (Laravel Forge)

For your first deployment, I **highly recommend Laravel Forge** because:

✅ Automated server setup
✅ Easy deployments (just push to GitHub)
✅ Automatic SSL certificates
✅ Built-in database backups
✅ Server monitoring
✅ Queue worker management
✅ Scheduled tasks (cron jobs)
✅ One-click rollbacks

**Total Cost:**
- Server: $12/month (DigitalOcean)
- Forge: $12/month
- Domain: ~$3/month
- Cloudflare: FREE
- **Total: ~$27/month**

---

## After Deployment Checklist

Once your site is live on frizzboss.ca:

### 1. Update Google Cloud Console

Add production redirect URI:
```
https://frizzboss.ca/auth/google/callback
```

### 2. Test Everything

- [ ] Homepage loads
- [ ] Google OAuth login works
- [ ] Admin login works (`lila@frizzboss.com`)
- [ ] Can create art classes
- [ ] Images upload correctly
- [ ] Stripe checkout works

### 3. Set Up Monitoring

**Free options:**
- **UptimeRobot** - https://uptimerobot.com/ (monitors if site is down)
- **Sentry** - https://sentry.io/ (error tracking)

### 4. Enable Cloudflare Features

In Cloudflare dashboard:
- **SSL/TLS** → Full (strict)
- **Speed** → Auto Minify (CSS, JS, HTML)
- **Caching** → Browser Cache TTL (4 hours)
- **Security** → Enable "Always Use HTTPS"

### 5. Set Up Backups

**If using Forge:**
- Server → Backups → Enable Daily Backups

**If manual:**
```bash
# Database backup script
sudo crontab -e

# Add this line (daily backup at 2 AM):
0 2 * * * pg_dump -U frizzboss_user frizzboss > /var/backups/frizzboss_$(date +\%Y\%m\%d).sql
```

---

## Troubleshooting

### Site not loading after DNS update
- Wait 24-48 hours for DNS propagation
- Check DNS: https://www.whatsmydns.net/
- Clear browser cache

### 500 Error
```bash
# Check logs
tail -f /var/www/frizzboss/storage/logs/laravel.log

# Common fixes:
sudo chown -R www-data:www-data /var/www/frizzboss
sudo chmod -R 775 /var/www/frizzboss/storage
sudo chmod -R 775 /var/www/frizzboss/bootstrap/cache
```

### Google OAuth not working
- Check `.env` has correct `APP_URL=https://frizzboss.ca`
- Verify Google Cloud Console has production redirect URI
- Clear config: `php artisan config:cache`

---

## Need Help?

Choose your path:

**Easiest:** Use Laravel Forge (I can walk you through it)
**More Control:** Manual VPS setup (I can help with commands)
**Budget:** Hetzner VPS (€4.51/month)

Let me know which option you want to go with and I'll help you step by step!
