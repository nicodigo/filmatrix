#!/bin/bash
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

vendor/bin/phinx migrate
php bin/sync_catalog.php --section=all --pages=1

exec apache2-foreground
