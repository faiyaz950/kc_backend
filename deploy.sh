#!/bin/bash

# KarbalaConnect Backend - Local Build Script for Shared Hosting Deployment
# This script prepares the application for upload to BigRock shared hosting

echo "🚀 KarbalaConnect Backend - Build for Production"
echo "================================================"

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the kc_backend directory"
    exit 1
fi

# Step 1: Install dependencies (production only)
echo ""
echo "📦 Step 1: Installing production dependencies..."
composer install --optimize-autoloader --no-dev

# Step 2: Clear all caches
echo ""
echo "🧹 Step 2: Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 3: Cache for production
echo ""
echo "⚡ Step 3: Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 4: Create deployment package
echo ""
echo "📁 Step 4: Creating deployment package..."

DEPLOY_DIR="deploy_package"
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR/kc_backend
mkdir -p $DEPLOY_DIR/public_html

# Copy Laravel files (excluding dev files)
rsync -av --progress . $DEPLOY_DIR/kc_backend \
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
    --exclude '*.md' \
    --exclude 'deploy.sh' \
    --exclude 'phpunit.xml' \
    --exclude '.editorconfig' \
    --exclude '.gitattributes' \
    --exclude '.gitignore'

# Copy public files to public_html
cp public_html_index.php $DEPLOY_DIR/public_html/index.php
cp public_html.htaccess $DEPLOY_DIR/public_html/.htaccess
cp public/robots.txt $DEPLOY_DIR/public_html/

# Copy .env.production as .env template
cp .env.production $DEPLOY_DIR/kc_backend/.env

# Create storage directory structure
mkdir -p $DEPLOY_DIR/kc_backend/storage/app/public
mkdir -p $DEPLOY_DIR/kc_backend/storage/framework/cache/data
mkdir -p $DEPLOY_DIR/kc_backend/storage/framework/sessions
mkdir -p $DEPLOY_DIR/kc_backend/storage/framework/views
mkdir -p $DEPLOY_DIR/kc_backend/storage/logs
mkdir -p $DEPLOY_DIR/kc_backend/bootstrap/cache

# Create .gitkeep files
touch $DEPLOY_DIR/kc_backend/storage/app/public/.gitkeep
touch $DEPLOY_DIR/kc_backend/storage/framework/cache/data/.gitkeep
touch $DEPLOY_DIR/kc_backend/storage/framework/sessions/.gitkeep
touch $DEPLOY_DIR/kc_backend/storage/framework/views/.gitkeep
touch $DEPLOY_DIR/kc_backend/storage/logs/.gitkeep
touch $DEPLOY_DIR/kc_backend/bootstrap/cache/.gitkeep

# Create zip file
echo ""
echo "📦 Step 5: Creating ZIP archive..."
cd $DEPLOY_DIR
zip -r ../kc_backend_deploy.zip .
cd ..

echo ""
echo "✅ Build complete!"
echo ""
echo "📁 Files created:"
echo "   - deploy_package/   (extracted files)"
echo "   - kc_backend_deploy.zip (ready for upload)"
echo ""
echo "📋 Next steps:"
echo "   1. Upload kc_backend/ folder to /home/username/ on BigRock"
echo "   2. Upload public_html/ contents to your domain's public_html or subdomain folder"
echo "   3. Update .env file with your database credentials"
echo "   4. Run database migrations (see DEPLOYMENT.md)"
echo ""
