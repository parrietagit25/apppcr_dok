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

# Límites de subida coherentes con rrhh_incapacidad_max_upload_bytes() (40 MB en app + margen POST)
RUN { \
    echo 'upload_max_filesize = 48M'; \
    echo 'post_max_size = 52M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 120'; \
  } > /usr/local/etc/php/conf.d/app-uploads.ini

# Copiar el código fuente
COPY ./src /var/www/html

# Apache: exponer variables de entorno a PHP (mod_php)
COPY docker/apache/pass-env.conf /etc/apache2/conf-available/app-pass-env.conf
RUN a2enconf app-pass-env

COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["apache2-foreground"]