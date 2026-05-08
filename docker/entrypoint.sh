#!/bin/bash
chown -R www-data:www-data /var/www/html/storage
mkdir -p /var/www/html/cache
chown -R www-data:www-data /var/www/html/cache
chmod -R 775 /var/www/html/storage

vendor/bin/phinx migrate

exec apache2-foreground
