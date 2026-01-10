FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html
WORKDIR /var/www/html

ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV SKIP_COMPOSER 0

# Node para Vite
RUN apk update && apk add --no-cache nodejs npm

# 1️⃣ INSTALAR DEPENDENCIAS PHP (ZIGGY VIVE AQUÍ)
RUN composer install --no-dev --optimize-autoloader

# 2️⃣ BUILD FRONTEND (YA EXISTE vendor/)
RUN npm install
RUN npm run build

CMD ["/start.sh"]
