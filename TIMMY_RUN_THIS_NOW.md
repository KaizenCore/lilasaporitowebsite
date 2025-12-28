# 🚨 URGENT: Fix Production Database Error

The site is getting a database error because it's trying to use SQLite instead of PostgreSQL.

---

## 🏃 Quick Fix (Run This Now)

### Option 1: Automated Script (Easiest)

```bash
# Navigate to your project directory
cd /var/www/frizzboss  # or wherever the app is installed

# Make script executable
chmod +x fix-production-database.sh

# Run the fix script
sudo ./fix-production-database.sh
```

This script will:
- ✅ Check PostgreSQL installation
- ✅ Create database if needed
- ✅ Update .env to use PostgreSQL
- ✅ Run migrations
- ✅ Seed admin user
- ✅ Set permissions
- ✅ Restart services

**Default database password created:** `FrizzB0ss2025!Secure`

---

### Option 2: Manual Fix (If script fails)

#### Step 1: Create PostgreSQL Database

```bash
sudo -u postgres psql

# Run these commands in PostgreSQL:
CREATE DATABASE frizzboss;
CREATE USER frizzboss_user WITH PASSWORD 'YourSecurePassword123!';
GRANT ALL PRIVILEGES ON DATABASE frizzboss TO frizzboss_user;
\c frizzboss
GRANT ALL ON SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO frizzboss_user;
\q
```

#### Step 2: Update .env File

```bash
nano .env
```

**Change these lines:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=frizzboss
DB_USERNAME=frizzboss_user
DB_PASSWORD=YourSecurePassword123!
```

Save (Ctrl+X, Y, Enter)

#### Step 3: Run Migrations

```bash
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
```

#### Step 4: Set Permissions

```bash
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

#### Step 5: Restart Services

```bash
sudo systemctl restart php8.4-fpm  # adjust version if needed
sudo systemctl restart nginx
```

---

## ✅ After Running the Fix

### Test These URLs:

1. **Homepage:** https://frizzboss.ca
   - Should load without errors

2. **Student Login:** https://frizzboss.ca/login
   - Should show Google OAuth button

3. **Admin Login:** https://frizzboss.ca/admin/login
   - Email: `lila@frizzboss.com`
   - Password: `password`
   - ⚠️ **Change this password immediately!**

---

## 🆘 If Still Getting Errors

### Check Laravel Logs:
```bash
tail -50 storage/logs/laravel.log
```

### Check Database Connection:
```bash
php artisan tinker
\DB::connection()->getPdo();
exit
```

### Verify .env is loaded:
```bash
php artisan config:clear
php artisan about
```

---

## 📞 Need Help?

Send this output:
```bash
php artisan about
php artisan migrate:status
sudo -u postgres psql -l | grep frizzboss
```

---

## 🔐 Security Notes

1. **Change admin password** after first login!
2. **Change database password** from default if using the script
3. Never commit `.env` file to git

---

**Run the script now and the site should be working in 2 minutes!** 🚀
