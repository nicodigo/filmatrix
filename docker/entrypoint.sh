#!/bin/bash
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
mkdir -p /var/www/html/cache
chown -R www-data:www-data /var/www/html/cache

vendor/bin/phinx migrate

exec apache2-foreground
