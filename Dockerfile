FROM php:8.2-cli

# Install required packages
RUN apt-get update && apt-get install -y \
    libzip-dev unzip sqlite3 wget git && docker-php-ext-install zip pdo pdo_sqlite

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate APP Key
RUN php artisan key:generate --ansi

# Expose port
EXPOSE 8000

# Run Laravel's server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
