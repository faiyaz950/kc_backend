# kc_backend — Hostinger Deployment (karbalaconnect.in)

## Server info

| | |
|---|---|
| SSH | `ssh -p 65002 u163472436@145.79.211.230` |
| Home | `/home/u163472436/` |
| Laravel | `/home/u163472436/kc_backend/` |
| Web root | `/home/u163472436/domains/karbalaconnect.in/public_html/` |

## 1. Local build (already done if you have `kc_backend_deploy.zip`)

```bash
cd kc_backend
./deploy.sh
```

## 2. Upload zip

```bash
scp -P 65002 kc_backend_deploy.zip u163472436@145.79.211.230:~/
```

## 3. hPanel before SSH

1. **PHP Configuration** → PHP **8.3**
2. **Databases** → create MySQL DB + user → update `~/kc_backend/.env` credentials

## 4. SSH — extract and place files

```bash
ssh -p 65002 u163472436@145.79.211.230

cd ~
unzip -o kc_backend_deploy.zip

rm -rf ~/kc_backend
mv deploy_package/kc_backend ~/kc_backend

cp deploy_package/public_html/index.php ~/domains/karbalaconnect.in/public_html/
cp deploy_package/public_html/.htaccess ~/domains/karbalaconnect.in/public_html/
cp deploy_package/public_html/robots.txt ~/domains/karbalaconnect.in/public_html/ 2>/dev/null || true

nano ~/kc_backend/.env
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD from hPanel

chmod +x ~/kc_backend/hostinger-post-deploy.sh
~/kc_backend/hostinger-post-deploy.sh
```

## 5. Verify

- https://karbalaconnect.in/up
- https://karbalaconnect.in/api/tracks

## Troubleshooting

- **500 error**: check `storage/logs/laravel.log`, permissions `775` on `storage` and `bootstrap/cache`
- **DB error**: confirm hPanel MySQL user has all privileges on the database
- **404**: ensure `.htaccess` is in `public_html` and PHP 8.3 is selected
