#!/bin/bash
set -e

# Fix Railway MPM bug
a2dismod mpm_event mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork 2>/dev/null || true

mkdir -p /tmp/php_sessions && chmod 777 /tmp/php_sessions

cat > /usr/local/etc/php/conf.d/session.ini <<EOF
session.save_path = /tmp/php_sessions
session.gc_maxlifetime = ${SESSION_GC_MAXLIFETIME:-1440}
session.gc_probability = 10
session.gc_divisor = 100
EOF

# Puerto
export APP_PORT=${PORT:-8080}
sed -i "s/__APP_PORT__/${APP_PORT}/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/^Listen 80$/Listen ${APP_PORT}/" /etc/apache2/ports.conf

mkdir -p /var/www/html/cache
chown -R www-data:www-data /var/www/html/cache
vendor/bin/phinx migrate

echo "Esperando a la base de datos..."
until php -r "new PDO('pgsql:host=${DB_HOSTNAME};port=${DB_PORT};dbname=${DB_DBNAME}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
  sleep 2
done

exec apache2-foreground