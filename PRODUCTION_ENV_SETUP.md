# Production Environment Setup for Timmy

## 🚨 Current Issue: Database Not Configured

The error shows Laravel is trying to use SQLite in production, but the database file doesn't exist. **Production should use PostgreSQL.**

---

## 📝 Complete .env Configuration for Production

Timmy needs to update the `.env` file on the server with these settings:

```env
# Application
APP_NAME=FrizzBoss
APP_ENV=production
APP_KEY=base64:EIO8YNvdJ69w295jB6rrQNmVjKQhQumU/r7RPpGweUs=
APP_DEBUG=false
APP_URL=https://frizzboss.ca

# Database - USE POSTGRESQL (NOT SQLITE!)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=frizzboss
DB_USERNAME=frizzboss_user
DB_PASSWORD=SECURE_PASSWORD_HERE

# Session - Use database (requires sessions table)
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database
QUEUE_CONNECTION=database

# Google OAuth (COPY FROM EXISTING .ENV)
GOOGLE_CLIENT_ID=your_google_client_id_from_existing_env
GOOGLE_CLIENT_SECRET=your_google_client_secret_from_existing_env
GOOGLE_REDIRECT_URI=https://frizzboss.ca/auth/google/callback

# Stripe (COPY FROM EXISTING .ENV)
STRIPE_KEY=your_stripe_key_from_existing_env
STRIPE_SECRET=your_stripe_secret_from_existing_env

# Mail (configure based on your email service)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@frizzboss.ca"
MAIL_FROM_NAME="FrizzBoss"
```

---

## 🔧 Step-by-Step Fix for Timmy

### Step 1: Create PostgreSQL Database

SSH into the server and run:

```bash
# Switch to postgres user
sudo -u postgres psql

# Create database and user
CREATE DATABASE frizzboss;
CREATE USER frizzboss_user WITH PASSWORD 'YourSecurePasswordHere123!';
GRANT ALL PRIVILEGES ON DATABASE frizzboss TO frizzboss_user;

# Grant schema permissions (PostgreSQL 15+)
\c frizzboss
GRANT ALL ON SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO frizzboss_user;

# Exit PostgreSQL
\q
```

### Step 2: Update .env File

```bash
# Navigate to project directory
cd /var/www/frizzboss  # or wherever the app is installed

# Edit .env file
nano .env
```

**Make sure these lines are set correctly:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=frizzboss
DB_USERNAME=frizzboss_user
DB_PASSWORD=YourSecurePasswordHere123!
```

Save and exit (Ctrl+X, then Y, then Enter)

### Step 3: Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Run Migrations

```bash
php artisan migrate --force
```

This will create all the database tables including:
- users
- sessions
- cache
- jobs
- art_classes
- bookings
- payments
- etc.

### Step 5: Seed the Database

```bash
php artisan db:seed --force
```

This creates:
- Admin user: `lila@frizzboss.com` / `password`
- Site settings

### Step 6: Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/frizzboss
sudo chmod -R 775 /var/www/frizzboss/storage
sudo chmod -R 775 /var/www/frizzboss/bootstrap/cache
```

### Step 7: Restart Services

```bash
# Restart PHP-FPM (adjust version if needed)
sudo systemctl restart php8.4-fpm

# Restart Nginx
sudo systemctl restart nginx
```

### Step 8: Test the Site

Visit: https://frizzboss.ca

You should now see the homepage without database errors!

---

## 🔍 Verify Database Connection

Test if the database is working:

```bash
php artisan tinker

# In tinker, run:
\DB::connection()->getPdo();
# Should show: PDO object

# Check if tables exist:
\DB::table('users')->count();
# Should show: 1 (the admin user)

# Exit tinker:
exit
```

---

## ❌ Common Issues & Solutions

### Issue 1: "SQLSTATE[08006] Connection refused"

**Cause:** PostgreSQL not running or wrong credentials

**Fix:**
```bash
# Check if PostgreSQL is running
sudo systemctl status postgresql

# Start if not running
sudo systemctl start postgresql

# Enable to start on boot
sudo systemctl enable postgresql
```

### Issue 2: "SQLSTATE[42501] Permission denied"

**Cause:** Database user doesn't have permissions

**Fix:**
```bash
sudo -u postgres psql -d frizzboss

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO frizzboss_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO frizzboss_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO frizzboss_user;

\q
```

### Issue 3: "Access denied for user"

**Cause:** Wrong password in .env

**Fix:**
- Double-check the password in `.env` matches what you set in PostgreSQL
- No quotes needed around the password in `.env`

### Issue 4: Still seeing SQLite error

**Cause:** Config cache not cleared

**Fix:**
```bash
php artisan config:clear
php artisan config:cache
```

---

## 🧪 Testing Checklist

After fixing, test these:

- [ ] Homepage loads: https://frizzboss.ca
- [ ] Login page: https://frizzboss.ca/login
- [ ] Admin login: https://frizzboss.ca/admin/login
- [ ] Can login as admin: `lila@frizzboss.com` / `password`
- [ ] Google OAuth works (click "Continue with Google")
- [ ] Can create a class in admin panel
- [ ] Images upload correctly

---

## 📊 Database Tables That Should Exist

After running migrations, check tables:

```bash
sudo -u postgres psql -d frizzboss

# List all tables
\dt

# Should see:
# - users
# - sessions
# - cache
# - cache_locks
# - jobs
# - job_batches
# - failed_jobs
# - password_reset_tokens
# - art_classes
# - bookings
# - payments
# - email_logs
# - site_settings
# - product_categories
# - products
# - orders
# - order_items
```

---

## 🔐 Security Recommendations

### 1. Change Default Admin Password

```bash
php artisan tinker

# Change password:
$user = \App\Models\User::where('email', 'lila@frizzboss.com')->first();
$user->password = 'NewSecurePassword123!';
$user->save();
exit
```

### 2. Use Strong Database Password

Don't use simple passwords like "password" or "123456"

Good example: `Frizz!B0ss$2025#Secure`

### 3. Restrict Database Access

Make sure PostgreSQL only accepts connections from localhost:

```bash
sudo nano /etc/postgresql/*/main/pg_hba.conf

# Should have this line:
# host    all    all    127.0.0.1/32    md5

sudo systemctl restart postgresql
```

---

## 📱 Quick Commands Reference

```bash
# View logs
tail -f /var/www/frizzboss/storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Check database connection
php artisan db:show

# List migrations status
php artisan migrate:status
```

---

## 🆘 If Still Having Issues

1. **Check Laravel logs:**
   ```bash
   tail -50 /var/www/frizzboss/storage/logs/laravel.log
   ```

2. **Check Nginx error log:**
   ```bash
   sudo tail -50 /var/log/nginx/error.log
   ```

3. **Check PHP-FPM log:**
   ```bash
   sudo tail -50 /var/log/php8.4-fpm.log
   ```

4. **Verify .env is loaded:**
   ```bash
   php artisan tinker
   config('database.default')
   # Should show: "pgsql"
   exit
   ```

---

## 📞 Need Help?

Send Timmy this info to help debug:

```bash
# Database connection info (sanitized)
php artisan db:show

# Environment info
php artisan about

# Migration status
php artisan migrate:status
```

---

**Once the database is set up correctly, the site should work perfectly!** 🚀
