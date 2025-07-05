FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    sqlite3 \
    wget \
    git \
    libsqlite3-dev \
    && docker-php-ext-configure pdo_sqlite --with-pdo-sqlite \
    && docker-php-ext-install zip pdo pdo_sqlite

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions for Laravel storage & cache
RUN chmod -R 777 storage bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Run Laravel's built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
