# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: install PHP dependencies (production only, no dev packages)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
# Copy only the dependency manifests first so this layer stays cached
# until composer.json / composer.lock actually change.
COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --ignore-platform-reqs

# ---------------------------------------------------------------------------
# Stage 2: application image (PHP-FPM)
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www

RUN docker-php-ext-install opcache \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p /data storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data /data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/data/database.sqlite

# Never run the application as root.
USER www-data

EXPOSE 9000
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
# Stage 3: web server image (Nginx serving static files, PHP via FastCGI)
# ---------------------------------------------------------------------------
FROM nginx:1.27-alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY public /var/www/public
EXPOSE 80
