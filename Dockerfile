# Use the official PHP image from the Docker Hub
FROM php:8.1-apache

# Copy application files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install dependencies if needed (uncomment if you need composer)
# RUN apt-get update && apt-get install -y \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev && \
#     docker-php-ext-configure gd --with-freetype --with-jpeg && \
#     docker-php-ext-install -j$(nproc) gd

# Install composer (uncomment if you need composer)
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions if needed (uncomment if you need mysqli, pdo_mysql)
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 80
EXPOSE 80
