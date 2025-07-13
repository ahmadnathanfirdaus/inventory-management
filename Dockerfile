# Use official PHP image with extensions needed for Laravel, Livewire, and Octane
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libbrotli-dev \
    supervisor

# Install Node.js (includes npm)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install NPM dependencies and build assets (for Livewire & Tailwind)
RUN npm install && npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose Laravel default serve port
EXPOSE 8000

# Start Laravel with artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
