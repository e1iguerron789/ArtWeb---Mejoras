FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl \
    libpng-dev libonig-dev libxml2-dev \
    nodejs npm

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY . /var/www
WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --optimize-autoloader --no-dev
RUN npm install
RUN npm run build
RUN cp .env.example .env
RUN php artisan key:generate
RUN php artisan migrate --force
RUN php artisan config:cache


CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
