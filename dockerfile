# Use an official PHP image with Apache
FROM php:8.3-apache

# Install necessary extensions
RUN apt-get update && apt-get install -y \
  libsqlite3-dev \
  unzip \
  && docker-php-ext-install pdo pdo_sqlite

# Enable mod_rewrite for Apache (required for Laravel/other frameworks)
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy application files to the container
COPY . /var/www/html

# Set proper permissions for the application
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in foreground mode
CMD ["apache2-foreground"]
