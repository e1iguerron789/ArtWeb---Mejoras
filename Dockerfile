FROM richarvey/nginx-php-fpm:3.1.6

# Copiar el proyecto
COPY . /var/www/html
WORKDIR /var/www/html

# Config básica
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV SKIP_COMPOSER 1

# Instalar Node + npm para compilar Vite (Vue)
RUN apk update && apk add --no-cache nodejs npm

# Build de frontend (Vite)
RUN npm install
RUN npm run build

# Arranque del contenedor
CMD ["/start.sh"]
