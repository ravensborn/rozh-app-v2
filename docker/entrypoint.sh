#!/bin/sh

composer install --no-progress --no-interaction

composer dump-autoload --optimize

selected_env=${SELECTED_ENV:-env.local}

cp "$selected_env" .env

echo "selected_env"

php artisan key:generate
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
exec docker-php-entrypoint "$@"
