#!/bin/sh

composer install --no-progress --no-interaction

composer dump-autoload --optimize

php artisan key:generate
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
exec docker-php-entrypoint "$@"
