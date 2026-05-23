#!/bin/bash
set -e

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running central migrations..."
php artisan migrate --force

echo "==> Seeding central database..."
php artisan db:seed --class=DatabaseSeeder --force

echo "==> Running tenant migrations..."
php artisan tenants:migrate --force 2>/dev/null || true

# Auto-create a demo tenant if both env vars are set and domain doesn't exist yet
if [ -n "${DEMO_TENANT_DOMAIN}" ] && [ -n "${DEMO_TENANT_NAME}" ]; then
    echo "==> Checking demo tenant [${DEMO_TENANT_NAME}] on domain [${DEMO_TENANT_DOMAIN}]..."
    php artisan tenant:create \
        --name="${DEMO_TENANT_NAME}" \
        --domain="${DEMO_TENANT_DOMAIN}" \
        || echo "    (tenant already exists or creation skipped)"
fi

# One-time tenant seed: set SEED_TENANT_ID=1002 in Railway vars, then remove after deploy
if [ -n "${SEED_TENANT_ID}" ]; then
    echo "==> Seeding tenant [${SEED_TENANT_ID}]..."
    php artisan tenants:seed --tenants="${SEED_TENANT_ID}" --force \
        || echo "    (seed failed or skipped)"
fi

echo "==> Starting PHP server..."
exec php -S 0.0.0.0:${PORT:-8000} -t public
