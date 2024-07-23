# Use the official PHP image from the Docker Hub
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Run composer install
RUN composer install --no-interaction --no-scripts --prefer-dist

# Expose port 80
EXPOSE 80
