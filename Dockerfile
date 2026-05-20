FROM node:22-bookworm AS frontend_build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-cli-bookworm
WORKDIR /app

# Install required system packages and PHP extensions.
# Only install extensions that are NOT already built into the official PHP image.
# Built-ins (no install needed): ctype, dom, fileinfo, filter, hash, openssl,
#   pcre, session, tokenizer, xml, curl, mbstring, pdo
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libicu-dev libzip-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    exif gd intl zip bcmath mbstring pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /app
COPY --from=frontend_build /app/public/build /app/public/build

# Install dependencies
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Set permissions
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache && \
    chmod -R a+rw storage bootstrap/cache

COPY start.sh /app/start.sh
RUN chmod +x /app/start.sh

EXPOSE 8000
CMD ["/app/start.sh"]
