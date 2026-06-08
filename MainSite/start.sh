#!/bin/ash
set -e

PUBLIC_DIR="/var/www/site/public"
CONF_DIR="/etc/nginx/conf.d"

is_mounted() {
    _path=$(readlink -f "$1" 2>/dev/null || echo "$1")
    grep -q " ${_path} " /proc/mounts 2>/dev/null
}

# Resolve persist root from env (shell only — generated-paths.php not written yet)
if [ -n "${PERSIST_DIR:-}" ]; then
    PERSIST_ROOT="${PERSIST_DIR}"
elif [ -n "${RAILWAY_VOLUME_MOUNT_PATH:-}" ]; then
    PERSIST_ROOT="${RAILWAY_VOLUME_MOUNT_PATH}"
else
    PERSIST_ROOT="${PUBLIC_DIR}"
fi

GALLERY_DIR="${PERSIST_ROOT}/gallery"
DATA_DIR="${PERSIST_ROOT}/data"

# Railway volume mounted directly on a leaf path
case "${RAILWAY_VOLUME_MOUNT_PATH:-}" in
    */gallery)
        GALLERY_DIR="${RAILWAY_VOLUME_MOUNT_PATH}"
        DATA_DIR="${PUBLIC_DIR}/data"
        ;;
    */data)
        DATA_DIR="${RAILWAY_VOLUME_MOUNT_PATH}"
        GALLERY_DIR="${PUBLIC_DIR}/gallery"
        ;;
esac

# Honor bind mounts already attached under public/ (never rm these)
if is_mounted "${PUBLIC_DIR}/gallery"; then
    GALLERY_DIR="${PUBLIC_DIR}/gallery"
fi
if is_mounted "${PUBLIC_DIR}/data"; then
    DATA_DIR="${PUBLIC_DIR}/data"
fi

mkdir -p "${GALLERY_DIR}" "${DATA_DIR}"

# Runtime paths for PHP (written before init-volumes.php runs)
cat > /var/www/site/generated-paths.php <<EOF
<?php
function persistRoot(): string { return '${PERSIST_ROOT}'; }
function galleryDir(): string { return '${GALLERY_DIR}'; }
function travelsFile(): string { return '${DATA_DIR}/travels.json'; }
EOF

export PERSIST_DIR="${PERSIST_ROOT}"

mkdir -p "${CONF_DIR}"

# Serve /gallery/ from persist dir when it is not under public/
if [ "${GALLERY_DIR}" != "${PUBLIC_DIR}/gallery" ]; then
    cat > "${CONF_DIR}/gallery.conf" <<EOF
location /gallery/ {
    alias ${GALLERY_DIR}/;
}
EOF
else
    rm -f "${CONF_DIR}/gallery.conf"
fi

echo "⟳ Gallery dir: ${GALLERY_DIR}"
echo "⟳ Data dir:    ${DATA_DIR}"
echo "⟳ Syncing git seed content into persistent storage..."
php /var/www/site/init-volumes.php

chown -R www-data:www-data "${GALLERY_DIR}" "${DATA_DIR}"

echo "⟳ Starting PHP-FPM..."
php-fpm &

echo "⟳ Starting Nginx..."
nginx -g "daemon off;"
