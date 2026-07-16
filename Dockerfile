FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN mkdir -p /tmp/php_sessions && chmod 777 /tmp/php_sessions
RUN echo "session.save_path = /tmp/php_sessions" > /usr/local/etc/php/conf.d/session.ini

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . .
RUN chmod -R a+r /var/www/html/src /var/www/html/views /var/www/html/public /var/www/html/vendor

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
