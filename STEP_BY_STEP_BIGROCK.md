# KarbalaConnect Backend - BigRock Deployment (Step by Step)

## Your Server Details
- **Domain:** karbalconnect.com
- **Home Directory:** /home2/hospi5ad
- **API URL (after deployment):** https://karbalconnect.com/api

---

## PART 1: MySQL Database Banayein (cPanel mein)

### Step 1.1: cPanel Open Karein
1. BigRock dashboard par "Go To cPanel" click karein
2. cPanel khulega

### Step 1.2: MySQL Database Create Karein
1. cPanel mein "MySQL Databases" dhundhein aur click karein
2. **Create New Database:**
   - Database name: `karbalaconnect` likhein
   - "Create Database" click karein
   - Full name hoga: `hospi5ad_karbalaconnect`

3. **Create New User:**
   - Username: `kcuser` likhein
   - Password: Strong password generate karein (ya khud likhein)
   - "Create User" click karein
   - Full username hoga: `hospi5ad_kcuser`
   - **PASSWORD NOTE KAREIN!**

4. **User ko Database se Connect Karein:**
   - "Add User To Database" section mein:
   - User: `hospi5ad_kcuser` select karein
   - Database: `hospi5ad_karbalaconnect` select karein
   - "Add" click karein
   - Next page par "ALL PRIVILEGES" checkbox tick karein
   - "Make Changes" click karein

### 📝 Note Down These Details:
```
DB_DATABASE=hospi5ad_karbalaconnect
DB_USERNAME=hospi5ad_kcuser
DB_PASSWORD=aapka_password_jo_banaya
DB_HOST=localhost
```

---

## PART 2: Files Upload Karein (File Manager)

### Step 2.1: Laravel Folder Create Karein
1. cPanel File Manager kholein
2. Home directory mein jayein (`/home2/hospi5ad`)
3. **"+ Folder"** click karein
4. Name: `kc_backend` likhein
5. "Create New Folder" click karein

### Step 2.2: Laravel Files Upload Karein
Ye folders/files upload karne hain `kc_backend` folder mein:

```
kc_backend/
├── app/                 ← UPLOAD
├── bootstrap/           ← UPLOAD
├── config/              ← UPLOAD
├── database/            ← UPLOAD
├── public/              ← UPLOAD (but index.php alag se modify karenge)
├── resources/           ← UPLOAD
├── routes/              ← UPLOAD
├── storage/             ← UPLOAD
├── vendor/              ← UPLOAD (ye bada hai ~50MB)
├── artisan              ← UPLOAD
├── composer.json        ← UPLOAD
└── .env                 ← UPLOAD (modified version)
```

**Upload karne ka tarika:**
1. File Manager mein `kc_backend` folder open karein
2. "Upload" button click karein
3. Apne local `kc_backend` folder se files select karein
4. Ya ZIP banake upload karein, phir Extract karein (faster method)

### Step 2.3: ZIP Method (Recommended - Faster)
1. Local machine par:
   ```bash
   cd /Users/faiyazmujtaba/StudioProjects/kc
   
   # Zip banayein (node_modules aur .git exclude)
   zip -r kc_backend_upload.zip kc_backend -x "kc_backend/node_modules/*" -x "kc_backend/.git/*" -x "kc_backend/storage/logs/*.log"
   ```

2. cPanel File Manager mein:
   - Home directory mein jayein
   - "Upload" click karein
   - `kc_backend_upload.zip` upload karein
   - Upload complete hone ke baad, zip file select karein
   - "Extract" click karein
   - Extract ho jayega

---

## PART 3: Domain Document Root Setup

### Step 3.0: Document Root Change Karein (Important!)
1. cPanel mein **"Domains"** section jayein
2. `karbalconnect.com` ke saamne **"Manage"** click karein
3. Document Root change karein: `/home2/hospi5ad/karbalconnect.com`
4. Save karein

### Step 3.1: karbalconnect.com Folder Banayein
1. File Manager mein Home directory (`/home2/hospi5ad`) jayein
2. **"+ Folder"** click karein
3. Name: `karbalconnect.com` likhein
4. "Create New Folder" click karein

### Step 3.2: index.php Create Karein
1. `karbalconnect.com` folder mein jayein
2. **"+ File"** click karein
3. Name: `index.php` likhein
4. File create hone ke baad, select karein aur **"Edit"** click karein
5. Ye code paste karein:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path to Laravel installation
$laravelPath = '/home2/hospi5ad/kc_backend';

// Maintenance mode check
if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require $laravelPath.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

6. "Save Changes" click karein

### Step 3.3: .htaccess Create Karein
1. `karbalconnect.com` folder mein rahein
2. **"+ File"** click karein
3. Name: `.htaccess` likhein
4. File create hone ke baad, select karein aur **"Edit"** click karein
5. Ye code paste karein:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security
Options -Indexes
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

6. "Save Changes" click karein

---

## PART 4: .env File Configure Karein

### Step 4.1: .env File Edit Karein
1. File Manager mein `/home2/hospi5ad/kc_backend` folder jayein
2. `.env` file select karein (agar nahi hai to `.env.example` copy karke `.env` rename karein)
3. **"Edit"** click karein
4. Ye content paste karein (apne database details ke saath):

```env
APP_NAME=KarbalaConnect
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://karbalconnect.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=hospi5ad_karbalaconnect
DB_USERNAME=hospi5ad_kcuser
DB_PASSWORD=AAPKA_PASSWORD_YAHAN

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync

CACHE_STORE=file

CLOUDINARY_URL=cloudinary://267766959368417:zJvEzbVOowkYOfXQghyvKrPD99s@dsrxoq9es
CLOUDINARY_CLOUD_NAME=dsrxoq9es
CLOUDINARY_API_KEY=267766959368417
CLOUDINARY_API_SECRET=zJvEzbVOowkYOfXQghyvKrPD99s

SANCTUM_STATEFUL_DOMAINS=karbalconnect.com,www.karbalconnect.com

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@karbalconnect.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
CORS_ALLOWED_ORIGINS=*
```

5. **"Save Changes"** click karein

---

## PART 5: Permissions Set Karein

### Step 5.1: Storage Folder Permissions
1. File Manager mein `/home2/hospi5ad/kc_backend` jayein
2. `storage` folder par right-click karein
3. **"Change Permissions"** click karein
4. Permission set karein: **775** (ya checkboxes se Owner/Group/World Read+Write+Execute)
5. "Change Permissions" click karein
6. **Important:** "Recurse into subdirectories" checkbox tick karein

### Step 5.2: Bootstrap/cache Folder Permissions
1. `bootstrap` folder open karein
2. `cache` folder par right-click karein
3. **"Change Permissions"** → **775**
4. "Change Permissions" click karein

---

## PART 6: App Key Generate Karein aur Migrations Run Karein

### Option A: Terminal/SSH Se (Agar Available Hai)
cPanel mein "Terminal" dhundhein, open karein, aur run karein:

```bash
cd ~/kc_backend
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### Option B: Web Route Se (Agar SSH Nahi Hai)

1. File Manager mein `/home2/hospi5ad/kc_backend/routes/web.php` edit karein
2. File ke end mein ye code add karein:

```php
// TEMPORARY SETUP ROUTES - DELETE AFTER USE
Route::get('/setup-kc-2024-secret', function () {
    try {
        // Generate key
        Artisan::call('key:generate', ['--force' => true]);
        $keyResult = "Key generated successfully\n";
        
        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $migrateResult = Artisan::output();
        
        return "<pre>$keyResult\nMigrations:\n$migrateResult</pre>";
    } catch (\Exception $e) {
        return "<pre>Error: " . $e->getMessage() . "</pre>";
    }
});

Route::get('/storage-link-kc-2024', function () {
    try {
        Artisan::call('storage:link');
        return "<pre>Storage link created!</pre>";
    } catch (\Exception $e) {
        return "<pre>Error: " . $e->getMessage() . "</pre>";
    }
});
```

3. Save karein
4. Browser mein visit karein:
   - `https://karbalconnect.com/setup-kc-2024-secret`
   - `https://karbalconnect.com/storage-link-kc-2024`

5. **IMPORTANT:** Setup complete hone ke baad, web.php se ye routes DELETE kar dein!

---

## PART 7: Test Karein

Browser mein ye URLs check karein:

1. **Health Check:**
   ```
   https://karbalconnect.com/up
   ```
   Expected: Page load hona chahiye

2. **API Test:**
   ```
   https://karbalconnect.com/api/tracks
   ```
   Expected: JSON response (empty array ya tracks list)

3. **Reciters Test:**
   ```
   https://karbalconnect.com/api/reciters
   ```

---

## PART 8: Flutter App Update Karein

`karbalaconnect/lib/config/app_config.dart` mein:

```dart
class AppConfig {
  static const bool isProduction = bool.fromEnvironment('dart.vm.product');

  static const String devApiUrl = 'http://192.168.1.28:8001/api';
  static const String prodApiUrl = 'https://karbalconnect.com/api';

  static String get baseUrl => isProduction ? prodApiUrl : devApiUrl;
}
```

---

## Troubleshooting

### 500 Error Aaye To:
1. `.env` mein `APP_DEBUG=true` karein temporarily
2. Error message dekhein
3. Fix karein, phir `APP_DEBUG=false` wapas karein

### 404 Error Aaye To:
1. `.htaccess` file check karein
2. `index.php` path check karein

### Database Error Aaye To:
1. Database credentials verify karein
2. User ko database se properly add kiya hai check karein

### Permission Error Aaye To:
1. `storage` aur `bootstrap/cache` folders 775 permissions check karein

---

## Files Structure After Deployment

```
/home2/hospi5ad/
├── kc_backend/              ← Laravel App (NOT web accessible)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/             ← 775 permissions
│   ├── vendor/
│   ├── .env                 ← Your config
│   └── artisan
│
└── karbalconnect.com/       ← Document Root (Web accessible)
    ├── index.php            ← Entry point
    └── .htaccess            ← Rewrite rules
```

