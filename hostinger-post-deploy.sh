#!/bin/bash
# Run on Hostinger via SSH after .env is configured

set -e

BACKEND_DIR="${HOME}/kc_backend"

# Hostinger CLI defaults to PHP 8.2; Laravel 13 needs 8.3+
if [ -x /opt/alt/php84/usr/bin/php ]; then
    PHP_BIN=/opt/alt/php84/usr/bin/php
elif [ -x /opt/alt/php83/usr/bin/php ]; then
    PHP_BIN=/opt/alt/php83/usr/bin/php
else
    PHP_BIN=php
fi

echo "Using PHP: $($PHP_BIN -v | head -1)"

if [ ! -f "${BACKEND_DIR}/artisan" ]; then
    echo "Error: ${BACKEND_DIR}/artisan not found."
    exit 1
fi

cd "${BACKEND_DIR}"

if grep -qE 'YOUR_HPANEL_|CHANGE_ME' .env 2>/dev/null; then
    echo "Error: Update DB_* and secrets in ~/kc_backend/.env first."
    exit 1
fi

if [ ! -d vendor ]; then
    if [ -f /usr/local/bin/composer ]; then
        $PHP_BIN /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
    else
        $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction
    fi
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    $PHP_BIN artisan key:generate --force
fi

chmod -R 775 storage bootstrap/cache

$PHP_BIN artisan migrate --force
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

WEB_DIR="${HOME}/domains/karbalaconnect.in/public_html"
cp public_html_index.php "${WEB_DIR}/index.php"
cp public_html.htaccess "${WEB_DIR}/.htaccess"

echo ""
echo "Done. Test: https://karbalaconnect.in/up"
