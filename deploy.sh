#!/bin/bash

# Auto-deployment script for FrizzBoss
# This runs automatically after git pull in production

echo "🚀 FrizzBoss Auto-Deployment Starting..."

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations (only if database is configured)
php artisan migrate --force 2>/dev/null && echo "✅ Migrations completed" || echo "⚠️  Migrations skipped (database not configured)"

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "✅ Deployment complete!"
