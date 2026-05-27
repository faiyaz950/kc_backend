#!/bin/bash
# One-time setup on Hostinger (run via SSH before first GitHub Actions deploy)

set -e

REPO="https://github.com/faiyaz950/kc_backend.git"
APP_DIR="${HOME}/kc_backend"
WEB_DIR="${HOME}/domains/karbalaconnect.in/public_html"

echo "=== kc_backend — Hostinger GitHub setup ==="

if [ ! -d "${WEB_DIR}" ]; then
    echo "Error: ${WEB_DIR} not found. Create karbalaconnect.in site in hPanel first."
    exit 1
fi

if [ -d "${APP_DIR}/.git" ]; then
    echo "Updating existing clone..."
    cd "${APP_DIR}"
    git pull origin main
else
    echo "Cloning ${REPO} ..."
    rm -rf "${APP_DIR}"
    git clone "${REPO}" "${APP_DIR}"
fi

cd "${APP_DIR}"

if [ ! -f .env ]; then
    cp .env.hostinger.example .env
    echo ""
    echo "Created .env from template. Edit it now:"
    echo "  nano ${APP_DIR}/.env"
    echo ""
    echo "Set DB_* and Cloudinary keys from hPanel, then run:"
    echo "  ${APP_DIR}/hostinger-post-deploy.sh"
    exit 0
fi

cp public_html_index.php "${WEB_DIR}/index.php"
cp public_html.htaccess "${WEB_DIR}/.htaccess"
cp -f public/robots.txt "${WEB_DIR}/robots.txt" 2>/dev/null || true

chmod +x hostinger-post-deploy.sh
./hostinger-post-deploy.sh
