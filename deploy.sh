#!/bin/bash

# KarbalaConnect Backend - Production build for Hostinger shared hosting

set -e

echo "KarbalaConnect Backend - Hostinger production build"
echo "===================================================="

if [ ! -f "artisan" ]; then
    echo "Error: Run this script from the kc_backend directory"
    exit 1
fi

echo ""
echo "Step 1: Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

echo ""
echo "Step 2: Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "Step 3: Creating deployment package..."

DEPLOY_DIR="deploy_package"
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR/kc_backend"
mkdir -p "$DEPLOY_DIR/public_html"

rsync -a . "$DEPLOY_DIR/kc_backend" \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude 'deploy_package' \
    --exclude 'tests' \
    --exclude '.env' \
    --exclude '.env.local' \
    --exclude 'storage/logs/*.log' \
    --exclude 'storage/framework/cache/data/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --exclude 'DEPLOYMENT.md' \
    --exclude 'STEP_BY_STEP_BIGROCK.md' \
    --exclude 'phpunit.xml' \
    --exclude '.editorconfig' \
    --exclude '.gitattributes' \
    --exclude '.gitignore' \
    --exclude 'kc_backend_deploy.zip' \
    --exclude 'database/database.sqlite'

cp public_html_index.php "$DEPLOY_DIR/public_html/index.php"
cp public_html.htaccess "$DEPLOY_DIR/public_html/.htaccess"
cp public/robots.txt "$DEPLOY_DIR/public_html/" 2>/dev/null || true
cp .env.production "$DEPLOY_DIR/kc_backend/.env"
cp HOSTINGER_DEPLOY.md "$DEPLOY_DIR/"
cp hostinger-post-deploy.sh "$DEPLOY_DIR/kc_backend/"

mkdir -p "$DEPLOY_DIR/kc_backend/storage/app/public"
mkdir -p "$DEPLOY_DIR/kc_backend/storage/framework/cache/data"
mkdir -p "$DEPLOY_DIR/kc_backend/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/kc_backend/storage/framework/views"
mkdir -p "$DEPLOY_DIR/kc_backend/storage/logs"
mkdir -p "$DEPLOY_DIR/kc_backend/bootstrap/cache"

touch "$DEPLOY_DIR/kc_backend/storage/app/public/.gitkeep"
touch "$DEPLOY_DIR/kc_backend/storage/framework/cache/data/.gitkeep"
touch "$DEPLOY_DIR/kc_backend/storage/framework/sessions/.gitkeep"
touch "$DEPLOY_DIR/kc_backend/storage/framework/views/.gitkeep"
touch "$DEPLOY_DIR/kc_backend/storage/logs/.gitkeep"
touch "$DEPLOY_DIR/kc_backend/bootstrap/cache/.gitkeep"

chmod +x "$DEPLOY_DIR/kc_backend/hostinger-post-deploy.sh"

echo ""
echo "Step 4: Creating ZIP archive..."
rm -f kc_backend_deploy.zip
(cd "$DEPLOY_DIR" && zip -rq ../kc_backend_deploy.zip .)

echo ""
echo "Build complete."
echo ""
echo "  kc_backend_deploy.zip  - upload to Hostinger"
echo "  HOSTINGER_DEPLOY.md    - full steps"
echo ""
echo "Upload:"
echo "  scp -P 65002 kc_backend_deploy.zip u163472436@145.79.211.230:~/"
echo ""
