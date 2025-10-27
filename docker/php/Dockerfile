FROM php:8.3-fpm

# Alap csomagok
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd exif pcntl bcmath opcache

# Composer telepítés
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Munkakönyvtár
WORKDIR /var/www

# Jogosultság
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]
