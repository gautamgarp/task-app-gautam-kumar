# Stage 1: Build backend (PHP + Laravel)
FROM php:8.2-fpm as backend-builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies without running scripts (artisan is not yet copied)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Run composer scripts and optimize autoload now that app files (including artisan) are present
RUN composer dump-autoload --optimize && php artisan package:discover --ansi || true

# Create Laravel cache and log directories with proper permissions
RUN mkdir -p /app/bootstrap/cache /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/storage/logs && \
    chmod -R 775 /app/bootstrap/cache /app/storage

# Expose port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
