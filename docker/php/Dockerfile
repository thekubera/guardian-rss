FROM php:8.3-fpm-alpine AS base

WORKDIR /var/www/html

# System dependencies
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    oniguruma-dev

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN php artisan key:generate --force || true

# Permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

FROM base AS production

CMD ["php-fpm"]
