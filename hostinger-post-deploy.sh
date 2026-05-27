#!/bin/bash
# Run on Hostinger via SSH after uploading and extracting kc_backend_deploy.zip

set -e

BACKEND_DIR="${HOME}/kc_backend"

if [ ! -f "${BACKEND_DIR}/artisan" ]; then
    echo "Error: ${BACKEND_DIR}/artisan not found. Run deploy steps from HOSTINGER_DEPLOY.md first."
    exit 1
fi

cd "${BACKEND_DIR}"

if grep -qE 'YOUR_HPANEL_|CHANGE_ME' .env 2>/dev/null; then
    echo "Error: Update DB_* and secrets in ~/kc_backend/.env first."
    exit 1
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Done. Test: https://karbalaconnect.in/up"
