FROM php:8.5-apache

# Habilita mod_rewrite, necesario para que apache no devuelva error 404 en cualquier ruta
# que no sea un archivo físico (front controller).
RUN a2enmod rewrite

# Reemplaza el virtual host por defecto de apache con el definido en vhost.conf. Cambia 
# de /var/www/html/ a /var/www/html/public, necesario para no exponer toda la raíz del proyecto
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde su imagen oficial
# Multi-stage build para extraer el binario sin instalar toda la imagen
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

WORKDIR /var/www/html

# Instalar dependencias sin dev en producción
RUN composer install --no-dev --optimize-autoloader


COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
