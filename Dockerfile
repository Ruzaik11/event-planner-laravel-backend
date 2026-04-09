FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN git config --global --add safe.directory /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf && apache2-foreground