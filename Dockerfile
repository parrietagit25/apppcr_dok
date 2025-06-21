FROM php:8.1-apache

# Instalar dependencias necesarias para GD + extensiones necesarias
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd mysqli pdo pdo_mysql

# Copiar el código fuente
COPY ./src /var/www/html