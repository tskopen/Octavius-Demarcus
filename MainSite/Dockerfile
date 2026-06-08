FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 22M" >> /usr/local/etc/php/conf.d/uploads.ini

RUN mkdir -p /run/nginx

WORKDIR /var/www/site

COPY public ./public

COPY nginx.conf /etc/nginx/nginx.conf
COPY start.sh /start.sh

RUN mkdir -p /var/www/site/public/gallery /var/www/site/public/data \
    && chmod +x /start.sh \
    && chown -R www-data:www-data /var/www/site/public

CMD ["/start.sh"]
