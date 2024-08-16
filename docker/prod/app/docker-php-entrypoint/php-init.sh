#!/bin/bash

cd /var/www/html

php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

php-fpm
