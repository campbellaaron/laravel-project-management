# Stage 1: Install dependencies
FROM php:8.2-fpm AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel application code
COPY . .

# Ensure necessary Laravel directories exist before installation
RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Copy composer files and install dependencies
RUN chmod +x artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-cache

# Install Node.js & npm (Latest LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Install frontend dependencies
COPY package.json package-lock.json ./
RUN npm install && npm run build

# Stage 2: Production Image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Copy built application files from the builder stage
COPY --from=builder /var/www/html /var/www/html

# Ensure Laravel directories exist and have correct permissions
RUN mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set permissions for artisan and Laravel framework files
RUN chmod +x /var/www/html/artisan

# Expose port 9000 (PHP-FPM)
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
