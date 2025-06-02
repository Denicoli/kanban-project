#!/bin/bash
set -e

composer install --no-dev --optimize-autoloader
npm install
npm run build

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force