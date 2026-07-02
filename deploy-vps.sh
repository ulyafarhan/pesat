#!/usr/bin/env bash
set -e

if [ -z "$1" ]; then
    echo "Usage: ./deploy-vps.sh user@vps-ip"
    echo "Example: ./deploy-vps.sh root@123.45.67.89"
    exit 1
fi

VPS="$1"
REMOTE_PATH="/var/www/pesat"

echo "============================================"
echo "  Deploy PESAT Laravel ke VPS"
echo "  Target: $VPS:$REMOTE_PATH"
echo "============================================"
echo ""

cd "$(dirname "${BASH_SOURCE[0]}")"

# Rsync hanya folder laravel/ ke VPS
rsync -avz --delete \
    --exclude='.env' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/framework/cache/**' \
    --exclude='storage/framework/sessions/**' \
    --exclude='storage/framework/views/**' \
    --exclude='storage/logs/**' \
    --exclude='storage/debugbar/**' \
    --exclude='.phpunit.result.cache' \
    --exclude='bootstrap/ssr' \
    ./laravel/ "$VPS:$REMOTE_PATH/"

echo ""
echo "============================================"
echo "  Selesai! Jalankan di VPS:"
echo "  ssh $VPS"
echo "  cd $REMOTE_PATH"
echo "  composer install --no-dev --optimize-autoloader"
echo "  php artisan migrate --force"
echo "  php artisan config:cache"
echo "  php artisan route:cache"
echo "  php artisan view:cache"
echo "  php artisan storage:link"
echo "============================================"
