#!/bin/bash
# Run on Hostinger via SSH after uploading and extracting kc_backend_deploy.zip

set -e

BACKEND_DIR="${HOME}/kc_backend"

if [ ! -f "${BACKEND_DIR}/artisan" ]; then
    echo "Error: ${BACKEND_DIR}/artisan not found. Run deploy steps from HOSTINGER_DEPLOY.md first."
    exit 1
fi

cd "${BACKEND_DIR}"

if grep -q "CHANGE_ME_IN_HPANEL" .env 2>/dev/null; then
    echo "Warning: Update DB_PASSWORD (and DB_* names) in ~/kc_backend/.env before continuing."
    echo "Press Ctrl+C to abort, or Enter to continue..."
    read -r _
fi

php artisan key:generate --force
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Done. Test: https://karbalaconnect.in/up"
