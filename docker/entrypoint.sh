#!/bin/bash
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

vendor/bin/phinx migrate

exec apache2-foreground
