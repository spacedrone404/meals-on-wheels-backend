# Use the official PHP image with Apache
FROM php:8.3-apache

# Enable Apache mod_rewrite (required if you use .htaccess for routing)
RUN a2enmod rewrite

# Install PostgreSQL libraries and PHP PDO extensions for Neon DB
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy your local PHP files into the Apache document root
# (Assuming your .php files are in the same directory as this Dockerfile)
COPY ./ /var/www/html/

RUN echo "Options +Indexes" > /var/www/html/.htaccess

# Expose port 80 so Render's proxy can route traffic to Apache
EXPOSE 80
