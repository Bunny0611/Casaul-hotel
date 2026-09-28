FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql gd zip intl bcmath opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Copy Nginx configuration template
COPY docker/nginx.conf /etc/nginx/sites-available/default

COPY . /var/www/html

# Create required framework storage directories first to avoid missing path exceptions during composer install
RUN mkdir -p /run /var/www/.postgresql storage/framework/{cache,data,sessions,views,testing} storage/logs bootstrap/cache \
    && composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader \
    && npm install --no-fund --no-audit \
    && npm run build \
    && php artisan storage:link \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build \
    && chmod 700 /var/www/.postgresql

EXPOSE 8000

# Use a startup script to cache configs using Railway's environment variables, then run PHP-FPM and Nginx concurrently
CMD php artisan migrate --force \
    && php artisan config:clear \
    && php artisan cache:clear \
    && nginx \
    && php-fpm