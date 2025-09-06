FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    autoconf \
    automake \
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
    curl \
    # For PDF->Image conversion
    ghostscript \
    imagemagick \
    imagemagick-dev \
    fontconfig \
    ttf-dejavu \
     # For Puppeteer/Chromium in Alpine
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

RUN pecl install imagick \
    && docker-php-ext-enable imagick

RUN php -m | grep imagick

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

# Install Imagick PHP extension (depends on ImageMagick & Ghostscript)
RUN printf "\n" | pecl install imagick || true \
        && docker-php-ext-enable imagick || true

# Allow ImageMagick to read PDFs (Alpine packages often disable by default)
RUN if [ -f /etc/ImageMagick-7/policy.xml ]; then \
            sed -i 's/<policy domain="coder" rights="none" pattern="PDF"\/>/<policy domain="coder" rights="read|write" pattern="PDF"\/>/g' /etc/ImageMagick-7/policy.xml; \
            sed -i 's/<policy domain="coder" rights="none" pattern="PS"\/>/<policy domain="coder" rights="read|write" pattern="PS"\/>/g' /etc/ImageMagick-7/policy.xml; \
            sed -i 's/<policy domain="coder" rights="none" pattern="EPS"\/>/<policy domain="coder" rights="read|write" pattern="EPS"\/>/g' /etc/ImageMagick-7/policy.xml; \
        fi; \
        if [ -f /etc/ImageMagick-6/policy.xml ]; then \
            sed -i 's/<policy domain="coder" rights="none" pattern="PDF"\/>/<policy domain="coder" rights="read|write" pattern="PDF"\/>/g' /etc/ImageMagick-6/policy.xml; \
            sed -i 's/<policy domain="coder" rights="none" pattern="PS"\/>/<policy domain="coder" rights="read|write" pattern="PS"\/>/g' /etc/ImageMagick-6/policy.xml; \
            sed -i 's/<policy domain="coder" rights="none" pattern="EPS"\/>/<policy domain="coder" rights="read|write" pattern="EPS"\/>/g' /etc/ImageMagick-6/policy.xml; \
        fi

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Only if app source code changed
COPY . /var/www/

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Install cron
RUN apk add --no-cache busybox-suid

# Copy package.json and package-lock.json first for better caching
COPY package*.json ./

# Install Puppeteer globally for PDF generation
RUN npm install -g puppeteer

# Tell Puppeteer to use the installed Chromium package
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Only if composer.lock changed
COPY composer.json composer.lock /var/www/
RUN composer install --prefer-dist  --no-scripts --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www


# Expose port 9000 and start php-fpm server
EXPOSE 9000
# CMD [ "php-fpm"]


# Add Laravel scheduler cron entry
# RUN echo "* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

# Start cron service
CMD ["sh", "-c", "crond -f & docker-php-entrypoint php-fpm"]