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

# --✅ Add your prod env variables here as ENV --
ENV APP_ENV=prod
ENV APP_SECRET=your_super_secret_here
ENV DATABASE_URL=mysql://user:password@host:3306/dbname
ENV JWT_SECRET_KEY=/var/www/html/config/jwt/private.pem
ENV JWT_PUBLIC_KEY=/var/www/html/config/jwt/public.pem
ENV JWT_PASSPHRASE=your_passphrase
ENV CORS_ALLOW_ORIGIN='^https?://.*$' 

# --✅ Install Symfony dependencies optimized for prod --
RUN composer install --no-dev --optimize-autoloader

# --✅ Dump prod env --
RUN composer dump-env prod

RUN mkdir -p /var/www/html/var /var/www/html/vendor \
    && chown -R www-data:www-data /var/www/html/var /var/www/html/vendor


# Set the document root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
