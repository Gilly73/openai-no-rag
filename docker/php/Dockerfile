# docker/php/Dockerfile
#php:8.2-fpm
FROM php:8.2.11-fpm

# 1. Install system dependencies and PHP extensions
RUN apt-get update \
 && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
# needed for redis extension
    autoconf \
    build-essential \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
 # remove build tools to keep image slim
 && apt-get purge -y autoconf build-essential \
 && apt-get autoremove -y \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 2.5 Create a non-root user and group
RUN groupadd -g 1000 appuser \
 && useradd -u 1000 -g appuser -m -s /bin/bash appuser

# 3. Set working directory
WORKDIR /var/www/html

# 4. Copy only composer files for caching
COPY src/composer.json src/composer.lock ./

# 5. Install without running scripts (so no artisan needed yet)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 6. Copy the rest of the application
COPY src ./

# 7. Now run the package discovery
RUN composer run-script post-autoload-dump

# 8. Fix permissions: make appuser owner of all files
RUN chown -R appuser:appuser /var/www/html \
 && chmod -R 755 /var/www/html

# 9. Switch to non-root user
USER appuser

# 10. Expose the port PHP-FPM listens on
EXPOSE 9000

# 11. launch
CMD ["php-fpm"]
