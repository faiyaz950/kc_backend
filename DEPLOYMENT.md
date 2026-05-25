# KarbalaConnect Backend - BigRock Shared Hosting Deployment Guide

## Prerequisites

1. BigRock shared hosting account with:
   - PHP 8.3+ support
   - MySQL database
   - SSH access (optional but recommended)
   - File Manager access

2. Local requirements:
   - Composer installed
   - PHP 8.3+ installed locally

## Folder Structure on Server

After deployment, your server structure should look like:

```
/home/username/
├── kc_backend/           # Laravel application (NOT accessible via web)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
│
└── public_html/          # Web root (or your subdomain folder)
    └── api/              # Optional: if using subdomain/subfolder for API
        ├── index.php     # Modified entry point
        └── .htaccess
```

## Step-by-Step Deployment

### Step 1: Prepare Local Build

```bash
cd kc_backend
chmod +x deploy.sh
./deploy.sh
```

This creates `kc_backend_deploy.zip` ready for upload.

### Step 2: Create MySQL Database on BigRock

1. Login to BigRock cPanel
2. Go to **MySQL Databases**
3. Create a new database (e.g., `username_karbala`)
4. Create a new database user with password
5. Add user to database with **ALL PRIVILEGES**
6. Note down:
   - Database name: `username_karbala`
   - Database user: `username_dbuser`
   - Database password: `your_password`
   - Host: `localhost`

### Step 3: Upload Files

**Option A: Using File Manager (Recommended for beginners)**

1. Login to BigRock cPanel
2. Open **File Manager**
3. Navigate to `/home/username/` (home directory, NOT public_html)
4. Upload and extract `kc_backend_deploy.zip`
5. Move the `kc_backend` folder to `/home/username/kc_backend`
6. Move contents of `public_html` folder to your desired location:
   - For main domain: `/home/username/public_html/`
   - For subdomain (e.g., api.yourdomain.com): `/home/username/api.yourdomain.com/`
   - For subfolder: `/home/username/public_html/api/`

**Option B: Using SSH (Faster)**

```bash
# Connect via SSH
ssh username@your-server.com

# Navigate to home directory
cd ~

# Upload zip via SCP (run this on your local machine)
scp kc_backend_deploy.zip username@your-server.com:~/

# On server: Extract
unzip kc_backend_deploy.zip

# Move files
mv deploy_package/kc_backend ~/kc_backend
mv deploy_package/public_html/* ~/public_html/api/
```

### Step 4: Configure Environment

1. Navigate to `/home/username/kc_backend/`
2. Edit `.env` file with your actual credentials:

```env
APP_NAME=KarbalaConnect
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com/api

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_karbala
DB_USERNAME=username_dbuser
DB_PASSWORD=your_database_password

SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### Step 5: Update index.php Path

Edit `/home/username/public_html/api/index.php` (or wherever you placed it):

```php
// Update this line to match your server structure
$laravelPath = __DIR__.'/../../kc_backend';

// Example paths:
// If index.php is in: /home/username/public_html/api/index.php
// And Laravel is in:  /home/username/kc_backend
// Then use: __DIR__.'/../../kc_backend'

// If index.php is in: /home/username/public_html/index.php
// And Laravel is in:  /home/username/kc_backend
// Then use: __DIR__.'/../kc_backend'
```

### Step 6: Set Permissions

Via SSH:
```bash
cd ~/kc_backend
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Or via File Manager: Right-click folders → Change Permissions → Set to 775

### Step 7: Generate App Key

**Option A: Via SSH**
```bash
cd ~/kc_backend
php artisan key:generate
```

**Option B: Manual**
1. On your local machine, run: `php artisan key:generate --show`
2. Copy the key (e.g., `base64:abc123...`)
3. Paste it in your server's `.env` file as `APP_KEY`

### Step 8: Run Migrations

**Option A: Via SSH**
```bash
cd ~/kc_backend
php artisan migrate --force
```

**Option B: Via Web Route (if no SSH)**

Temporarily add this to `routes/web.php`:
```php
Route::get('/run-migrations-secret-key-12345', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrations completed!';
});
```

Visit `https://yourdomain.com/api/run-migrations-secret-key-12345` once, then REMOVE this route immediately.

### Step 9: Create Storage Link

**Option A: Via SSH**
```bash
cd ~/kc_backend
php artisan storage:link
```

**Option B: Manual Symlink**
Create a symbolic link from `public_html/api/storage` to `kc_backend/storage/app/public`

### Step 10: Test API

Visit these URLs to verify:
- Health check: `https://yourdomain.com/api/up`
- Tracks list: `https://yourdomain.com/api/api/tracks`
- Reciters list: `https://yourdomain.com/api/api/reciters`

## Troubleshooting

### 500 Internal Server Error
1. Check `.htaccess` is properly uploaded
2. Verify PHP version is 8.3+
3. Check storage folder permissions (775)
4. Enable error display temporarily in `.env`: `APP_DEBUG=true`
5. Check `storage/logs/laravel.log` for errors

### 404 Not Found
1. Verify `mod_rewrite` is enabled
2. Check `.htaccess` is present
3. Verify `index.php` path is correct

### Database Connection Error
1. Verify database credentials in `.env`
2. Ensure database user has proper permissions
3. Try `localhost` vs `127.0.0.1` for DB_HOST

### CORS Errors
1. Update `config/cors.php` allowed_origins if needed
2. Ensure proper headers in `.htaccess`

### Storage/Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Updating the Application

1. Create new build locally: `./deploy.sh`
2. Backup server files
3. Upload new `kc_backend` folder (excluding `.env`)
4. Run migrations if needed
5. Clear caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Strong database password
- [ ] `.env` file not accessible via web
- [ ] SSL certificate installed (HTTPS)
- [ ] Removed any debug routes

## API Endpoints Reference

### Public Endpoints
- `GET /api/tracks` - List all tracks
- `GET /api/tracks/{id}` - Get single track
- `GET /api/reciters` - List all reciters
- `GET /api/reciters/{id}` - Get single reciter
- `GET /api/anjumans` - List all anjumans
- `GET /api/anjumans/{id}` - Get single anjuman
- `GET /api/anjumans/{id}/tracks` - Get anjuman tracks
- `POST /api/register` - User registration
- `POST /api/login` - User login

### Protected Endpoints (require Bearer token)
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user
- `PUT /api/me` - Update profile
- `POST /api/favorites/{trackId}` - Add favorite
- `DELETE /api/favorites/{trackId}` - Remove favorite
- `POST /api/recently-played/{trackId}` - Add to recently played
- `POST /api/tracks/{id}/play` - Increment play count

### Admin Endpoints (require admin role)
- CRUD operations for tracks, reciters, anjumans, users

## Support

If you face any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. BigRock error logs in cPanel
3. Browser developer console for CORS/network errors
