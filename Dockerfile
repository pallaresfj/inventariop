FROM php:8.3-fpm-alpine AS php_base

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache bash curl git unzip icu-libs libzip oniguruma libxml2 \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev libxml2-dev linux-headers \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring bcmath intl zip exif pcntl opcache \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


FROM php_base AS php_vendor

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --classmap-authoritative --no-dev


FROM node:22-alpine AS node_assets

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build


FROM php_base AS runtime

WORKDIR /var/www/html

RUN apk add --no-cache nginx supervisor \
    && mkdir -p /run/nginx /etc/nginx/conf.d /etc/supervisor/conf.d

COPY . /var/www/html
COPY --from=php_vendor /var/www/html/vendor /var/www/html/vendor
COPY --from=node_assets /var/www/html/public/build /var/www/html/public/build

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/site.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord/supervisord.conf /etc/supervisord.conf
COPY docker/supervisord/web.conf /etc/supervisor/conf.d/web.conf
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
