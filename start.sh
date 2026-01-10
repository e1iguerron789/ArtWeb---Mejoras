#!/usr/bin/env bash
set -e

php artisan key:generate --force || true

php artisan migrate --force

php artisan config:clear
php artisan config:cache
php artisan route:cache

chmod -R 775 storage bootstrap/cache || true
