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

# Optimize for production (skip config:cache to allow bootstrap overrides)
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true
php artisan event:cache 2>/dev/null || true

# Set permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "✅ Deployment complete!"
