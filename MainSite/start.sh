#!/bin/ash
set -e

PUBLIC_DIR="/var/www/site/public"
PERSIST_DIR=$(php -r "require '/var/www/site/persist-path.php'; echo persistRoot();")

export PERSIST_DIR

mkdir -p "${PERSIST_DIR}/gallery" "${PERSIST_DIR}/data"

# When using an external persist dir (Railway volume), link public URLs to it
if [ "$PERSIST_DIR" != "$PUBLIC_DIR" ]; then
    rm -rf "${PUBLIC_DIR}/gallery" "${PUBLIC_DIR}/data"
    ln -sf "${PERSIST_DIR}/gallery" "${PUBLIC_DIR}/gallery"
    ln -sf "${PERSIST_DIR}/data" "${PUBLIC_DIR}/data"
else
    mkdir -p "${PUBLIC_DIR}/gallery" "${PUBLIC_DIR}/data"
fi

echo "⟳ Persist root: ${PERSIST_DIR}"
echo "⟳ Syncing git seed content into persistent storage..."
php /var/www/site/init-volumes.php

chown -R www-data:www-data "${PERSIST_DIR}/gallery" "${PERSIST_DIR}/data"

echo "⟳ Starting PHP-FPM..."
php-fpm &

echo "⟳ Starting Nginx..."
nginx -g "daemon off;"
