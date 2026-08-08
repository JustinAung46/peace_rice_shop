FROM php:8.2-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Leverage Docker cache: Install vendor packages before copying application code
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application source code
COPY . .

# Generate optimized autoloader after full application source is copied
RUN composer dump-autoload --optimize --no-scripts

# Set permissions for Laravel runtime directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]