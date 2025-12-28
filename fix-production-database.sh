#!/bin/bash

# FrizzBoss Production Database Fix Script
# Run this script to fix the SQLite -> PostgreSQL database error

echo "========================================="
echo "FrizzBoss Production Database Fix"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check if PostgreSQL is installed
echo -e "${YELLOW}[1/8] Checking PostgreSQL installation...${NC}"
if ! command -v psql &> /dev/null; then
    echo -e "${RED}ERROR: PostgreSQL is not installed!${NC}"
    echo "Install it with: sudo apt install postgresql postgresql-contrib"
    exit 1
fi
echo -e "${GREEN}✓ PostgreSQL is installed${NC}"
echo ""

# Step 2: Check if database exists
echo -e "${YELLOW}[2/8] Checking if database exists...${NC}"
DB_EXISTS=$(sudo -u postgres psql -tAc "SELECT 1 FROM pg_database WHERE datname='frizzboss'")
if [ "$DB_EXISTS" != "1" ]; then
    echo -e "${YELLOW}Database 'frizzboss' does not exist. Creating it...${NC}"

    # Create database
    sudo -u postgres psql <<EOF
CREATE DATABASE frizzboss;
CREATE USER frizzboss_user WITH PASSWORD 'FrizzB0ss2025!Secure';
GRANT ALL PRIVILEGES ON DATABASE frizzboss TO frizzboss_user;
\c frizzboss
GRANT ALL ON SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO frizzboss_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO frizzboss_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO frizzboss_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO frizzboss_user;
EOF

    echo -e "${GREEN}✓ Database created with user: frizzboss_user${NC}"
    echo -e "${GREEN}✓ Default password: FrizzB0ss2025!Secure${NC}"
    echo -e "${YELLOW}⚠ IMPORTANT: Update .env with this password!${NC}"
else
    echo -e "${GREEN}✓ Database 'frizzboss' already exists${NC}"
fi
echo ""

# Step 3: Update .env file
echo -e "${YELLOW}[3/8] Checking .env configuration...${NC}"
if [ ! -f .env ]; then
    echo -e "${RED}ERROR: .env file not found!${NC}"
    echo "Copy .env.production to .env and update the database password"
    exit 1
fi

# Check if .env has correct DB_CONNECTION
DB_CONN=$(grep "^DB_CONNECTION=" .env | cut -d'=' -f2)
if [ "$DB_CONN" != "pgsql" ]; then
    echo -e "${YELLOW}Updating .env to use PostgreSQL...${NC}"
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
    echo -e "${GREEN}✓ Updated DB_CONNECTION to pgsql${NC}"
else
    echo -e "${GREEN}✓ .env already configured for PostgreSQL${NC}"
fi
echo ""

# Step 4: Clear caches
echo -e "${YELLOW}[4/8] Clearing Laravel caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

# Step 5: Run migrations
echo -e "${YELLOW}[5/8] Running database migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed${NC}"
else
    echo -e "${RED}ERROR: Migrations failed!${NC}"
    echo "Check your database credentials in .env"
    exit 1
fi
echo ""

# Step 6: Seed database
echo -e "${YELLOW}[6/8] Seeding database...${NC}"
php artisan db:seed --force
echo -e "${GREEN}✓ Database seeded${NC}"
echo -e "${GREEN}  Admin user: lila@frizzboss.com${NC}"
echo -e "${GREEN}  Password: password${NC}"
echo -e "${YELLOW}  ⚠ Change this password after first login!${NC}"
echo ""

# Step 7: Set permissions
echo -e "${YELLOW}[7/8] Setting file permissions...${NC}"
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

# Step 8: Restart services
echo -e "${YELLOW}[8/8] Restarting services...${NC}"

# Try to detect PHP-FPM version
PHP_FPM_SERVICE=""
for version in 8.4 8.3 8.2 8.1; do
    if systemctl list-units --type=service | grep -q "php${version}-fpm"; then
        PHP_FPM_SERVICE="php${version}-fpm"
        break
    fi
done

if [ -n "$PHP_FPM_SERVICE" ]; then
    sudo systemctl restart "$PHP_FPM_SERVICE"
    echo -e "${GREEN}✓ Restarted $PHP_FPM_SERVICE${NC}"
else
    echo -e "${YELLOW}⚠ Could not detect PHP-FPM service, restart manually${NC}"
fi

# Restart Nginx
if systemctl list-units --type=service | grep -q nginx; then
    sudo systemctl restart nginx
    echo -e "${GREEN}✓ Restarted Nginx${NC}"
fi
echo ""

# Final status
echo "========================================="
echo -e "${GREEN}✓ Production database setup complete!${NC}"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Test the website: https://frizzboss.ca"
echo "2. Login as admin: lila@frizzboss.com / password"
echo "3. Change the admin password immediately!"
echo "4. Test Google OAuth login"
echo ""
echo "If you see errors, check logs:"
echo "  Laravel: tail -f storage/logs/laravel.log"
echo "  Nginx: sudo tail -f /var/log/nginx/error.log"
echo ""
