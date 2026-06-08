#!/bin/ash
set -e

GALLERY_VOL="/var/www/site/public/gallery"
DATA_VOL="/var/www/site/public/data"

mkdir -p "${GALLERY_VOL}" "${DATA_VOL}"

echo "⟳ Syncing git seed content into persistent volumes..."
php /var/www/site/init-volumes.php

chown -R www-data:www-data "${GALLERY_VOL}" "${DATA_VOL}"

echo "⟳ Starting PHP-FPM..."
php-fpm &

echo "⟳ Starting Nginx..."
nginx -g "daemon off;"
