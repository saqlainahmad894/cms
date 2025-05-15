# Dockerfile

FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git zip unzip libicu-dev libpq-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip xml

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Symfony dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Set the document root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
