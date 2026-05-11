#!/bin/bash
mkdir -p /var/www/html/cache
chown -R www-data:www-data /var/www/html/cache

echo "Esperando a la base de datos..."
until php -r "new PDO('pgsql:host=${DB_HOSTNAME};port=${DB_PORT};dbname=${DB_DBNAME}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
  sleep 2
done

vendor/bin/phinx migrate

exec apache2-foreground
