#!/bin/sh
set -e

echo "Starting Laravel application..."

# Wait for database to be ready (if DATABASE_URL is set)
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    sleep 5
fi

# Create storage directories if they don't exist
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is not set!"
fi

# Clear and cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (optional - can be disabled via env var)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Link storage
php artisan storage:link 2>/dev/null || true

echo "Application ready!"

# Execute the main command
exec "$@"
