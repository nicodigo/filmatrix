#!/bin/bash
set -e

# Fix Railway MPM bug (re-enables mpm_event at runtime)
a2dismod mpm_event mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork 2>/dev/null || true

# Railway inyecta PORT; localmente se usa 8080
export APP_PORT=${PORT:-8080}

# Hacer que Apache escuche en ese puerto
sed -i "s/^Listen 80$/Listen ${APP_PORT}/" /etc/apache2/ports.conf

mkdir -p /var/www/html/cache
chown -R www-data:www-data /var/www/html/cache

echo "Esperando a la base de datos..."
until php -r "new PDO('pgsql:host=${DB_HOSTNAME};port=${DB_PORT};dbname=${DB_DBNAME}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
  sleep 2
done

exec apache2-foreground
