FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    build-base \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    xml

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Only if app source code changed
COPY . /var/www/

# Only if composer.lock changed
COPY composer.json composer.lock /var/www/
RUN composer install --prefer-dist  --no-scripts --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www


# Expose port 9000 and start php-fpm server
EXPOSE 9000
# CMD [ "php-fpm"]

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Install cron
RUN apk add --no-cache busybox-suid

# Add Laravel scheduler cron entry
# RUN echo "* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

# Start cron service
CMD ["sh", "-c", "crond -f & docker-php-entrypoint php-fpm"]