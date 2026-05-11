FROM php:8.4-apache

# Extensiones según tu proyecto
RUN apt-get update && apt-get install -y libpq-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && apt-get clean

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# mod_rewrite para URLs limpias
RUN a2enmod rewrite
RUN a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork

WORKDIR /var/www/html

# Dependencias primero (aprovecha cache de Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Código
COPY . .

# Configuración Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Entrypoint que maneja $PORT
COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
