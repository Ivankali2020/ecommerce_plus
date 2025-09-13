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
    fontconfig \
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

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Install cron
RUN apk add --no-cache busybox-suid

# Copy package.json and package-lock.json first for better caching
COPY package*.json ./

# Install Puppeteer globally for PDF generation
RUN npm install -g puppeteer

# Install Myanmar fonts for proper text rendering
RUN mkdir -p /usr/share/fonts/truetype/myanmar && \
    wget -O /usr/share/fonts/truetype/myanmar/NotoSansMyanmar-Regular.ttf \
    "https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansMyanmar/NotoSansMyanmar-Regular.ttf" && \
    wget -O /usr/share/fonts/truetype/myanmar/NotoSansMyanmar-Bold.ttf \
    "https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansMyanmar/NotoSansMyanmar-Bold.ttf" && \
    fc-cache -fv

# CRITICAL FIX: Create Chrome directories to prevent crashpad handler error
RUN CHROME_DIRS="/var/www/.local /var/www/.config /var/www/.cache /var/www/.pki" && \
    mkdir -p ${CHROME_DIRS} && \
    mkdir -p /var/www/.config/google-chrome/Crashpad && \
    chown -R www-data:www-data ${CHROME_DIRS}

# Create directories for Chromium data in production
RUN mkdir -p /tmp/chromium-user-data /tmp/chromium-data /tmp/chromium-cache && \
    chmod 1777 /tmp/chromium-user-data /tmp/chromium-data /tmp/chromium-cache

# Tell Puppeteer to use the installed Chromium package
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser \
    CHROME_BIN=/usr/bin/chromium-browser \
    CHROME_PATH=/usr/bin/chromium-browser \
    NODE_OPTIONS="--max-old-space-size=4096"

# Copy composer files first for better caching
COPY composer.json composer.lock /var/www/

# Install composer dependencies without running scripts to avoid Laravel booting
RUN composer install --prefer-dist --no-scripts --no-interaction --no-dev

# Copy application files
COPY . /var/www/

# Run composer scripts after all files are in place
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Start cron service
CMD ["sh", "-c", "crond -f & docker-php-entrypoint php-fpm"]