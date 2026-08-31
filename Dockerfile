FROM php:8.2-apache

# Install cURL extension and dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]