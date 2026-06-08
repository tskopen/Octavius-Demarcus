FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx \
    && mkdir -p /etc/nginx/conf.d \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 22M" >> /usr/local/etc/php/conf.d/uploads.ini

RUN mkdir -p /run/nginx

WORKDIR /var/www/site

COPY public ./public
COPY persist-path.php ./persist-path.php
COPY init-volumes.php ./init-volumes.php

# Seed copies from git — merged into mounted volumes on each container start
RUN mkdir -p seed/gallery seed/data \
    && cp -a public/gallery/. seed/gallery/ \
    && cp -a public/data/. seed/data/

COPY nginx.conf /etc/nginx/nginx.conf
COPY start.sh /start.sh

RUN chmod +x /start.sh \
    && chown -R www-data:www-data /var/www/site/public

CMD ["/start.sh"]
