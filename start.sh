#!/bin/bash
set -e

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running central migrations..."
php artisan migrate --force

echo "==> Running tenant migrations..."
php artisan tenants:migrate --force 2>/dev/null || true

echo "==> Starting PHP server..."
exec php -S 0.0.0.0:${PORT:-8000} -t public
