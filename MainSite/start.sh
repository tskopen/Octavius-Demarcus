#!/bin/ash

echo "⟳ Starting PHP-FPM..."
php-fpm &


echo "⟳ Starting Nginx..."
nginx -g "daemon off;"
