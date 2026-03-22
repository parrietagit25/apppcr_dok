FROM php:8.1-apache

# GD + HEIC/HEIF en servidor (conversión sin pedir cambios al usuario)
# libheif1 + imagemagick + ffmpeg/heif-convert para fallback si php-imagick no lee HEIC
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libmagickwand-dev \
    imagemagick \
    libheif1 \
    libheif-examples \
    ffmpeg \
    zip \
    unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd mysqli pdo pdo_mysql \
 && printf '\n' | pecl install imagick \
 && docker-php-ext-enable imagick \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar el código fuente
COPY ./src /var/www/html