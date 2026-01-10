#!/usr/bin/env bash
set -e

echo "Running composer install..."
composer install --no-dev --optimize-autoloader

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Fixing permissions..."
chmod -R 775 storage bootstrap/cache || true
